<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package     block_course_recycle
 * @category    blocks
 * @author      Valery Fremaux (valery.fremaux@gmail.com)
 * @copyright   1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_course_recycle;

defined('MOODLE_INTERNAL') || die();

use \StdClass;
use \context;
use \context_block;
use \context_system;

require_once($CFG->dirroot.'/blocks/course_recycle/lib.php');
require_once($CFG->dirroot.'/lib/coursecatlib.php'); // until 3.5

class course_recycler {

    public static function get_instances(&$globals) {
        global $DB;

        $page = optional_param('page', 0, PARAM_INT);

        $sql = "
            SELECT
                c.shortname,
                c.fullname,
                c.idnumber,
                bi.configdata
            FROM
                {course} c,
                {context} ctx,
                {block_instances} bi
            WHERE
                c.id = ctx.instanceid AND
                ctx.contextlevel = 50 AND
                ctx.id = bi.parentcontextid AND
                bi.blockname = 'course_recycle'
        ";

        $instances = $DB->get_records_sql($sql, array());

        $globalconfig = get_config('block_course_recycle');

        $globals['throw'] = 0;
        $globals['archive'] = 0;
        $globals['keep'] = 0;
        $globals['reset'] = 0;
        $globals['unset'] = 0;
        foreach ($instances as $id => $instance) {
            $config = unserialize(base64_decode($instance->configdata));

            if ($config) {
                $recycleaction = $config->recycleaction;
            } else {
                $recycleaction = $globalconfig->defaultaction;
            }

            $instances[$id]->recycle = $recycleaction;

            switch ($recycleaction) {
                case 'throw': {
                    $globals['throw']++;
                    break;
                }

                case 'archive': {
                    $globals['archive']++;
                    break;
                }

                case 'keep': {
                    $globals['keep']++;
                    break;
                }

                case 'reset': {
                    $globals['reset']++;
                    break;
                }

                default:
                    $globals['unset']++;
            }
        }

        return $instances;
    }

    public static function get_status_list() {
        static $fullstatuslist;

        if (is_null($fullstatuslist)) {

            $fullstatuslist = [
                RECYCLE_RQFA => get_string('RequestForArchive', 'block_course_recycle'),
                RECYCLE_DONE => get_string('Done', 'block_course_recycle'),
                RECYCLE_FAILED => get_string('Failed', 'block_course_recycle'),
                RECYCLE_STAY => get_string('Stay', 'block_course_recycle'),
                RECYCLE_RESET => get_string('Reset', 'block_course_recycle'),
                RECYCLE_CLONE => get_string('Clone', 'block_course_recycle'),
                RECYCLE_CLONETANDRESET => get_string('CloneAndReset', 'block_course_recycle'),
                RECYCLE_ARCHIVE => get_string('Archive', 'block_course_recycle'),
                RECYCLE_CLONEARCHIVEANDRESET => get_string('CloneArchiveAndReset', 'block_course_recycle'),
                RECYCLE_DELETE => get_string('CloneArchiveAndReset', 'block_course_recycle')
            ];
        }

        return $fullstatuslist;
    }

    public static function add_to_courses() {
        global $DB;

        $allcourses = $DB->get_records('course');

        if ($allcourses) {
            $added = false;
            foreach ($allcourses as $course) {
                $context = context_course::instance($course->id);
                $params = array('parentcontextid' => $context->id, 'blockname' => 'course_recycle');
                if (!$recycleinstance = $DB->get_records('block_instances', $params)) {
                    if ($course->format != 'page' && $course->format != 'singleactivity') {
                        mtrace('Adding course recycle instance to course ['.$course->id.'] '.$course->fullname);
                        self::add_block_to_course($context, 'side-post', '-9');
                        $added = true;
                    } else if ($course->format == 'page') {
                        // Special processing.
                        // TODO : finish.
                    }
                }
            }

            if (!$added) {
                mtrace('No course needed a course_recycle block');
            }

        } else {
            mtrace('No courses');
        }
    }

    public static function add_block_to_course($context, $region, $weight, $showinsubcontexts = false, $pagetypepattern = NULL, $subpagepattern = NULL) {
        global $DB;

        if (empty($pagetypepattern)) {
            $pagetypepattern = '*';
        }

        $blockinstance = new stdClass;
        $blockinstance->blockname = 'course_recycle';
        $blockinstance->parentcontextid = $context->id;
        $blockinstance->showinsubcontexts = !empty($showinsubcontexts);
        $blockinstance->pagetypepattern = $pagetypepattern;
        $blockinstance->subpagepattern = $subpagepattern;
        $blockinstance->defaultregion = $region;
        $blockinstance->defaultweight = $weight;
        $blockinstance->configdata = '';
        $blockinstance->id = $DB->insert_record('block_instances', $blockinstance);

        // Ensure the block context is created.
        context_block::instance($blockinstance->id);
    }

    /**
     * Discover new finished courses that ar not yet marked.
     */
    public static function task_discover_finished($interactive = false) {
        self::discover_finished_courses($interactive);
        self::process_undecided_courses($interactive);
    }

    /*
     *
     */
    public static function discover_finished_courses($interactive) {
        global $DB, $USER;
        // Discover using several heuristics.

        $config = get_config('block_course_recycle');
        $systemcontext = context_system::instance();

        $finished = [];
        $notifieduserids = [];

        if (!empty($config->policyenddate)) {

            if ($interactive) {
                mtrace("Checking by end date...");
            }

            // Check end date of the course (explicit)
            $sql = "
                SELECT
                    c.id,
                    c.shortname,
                    c.enddate,
                    'bydate' AS reason
                FROM
                    {course} c
                LEFT JOIN
                    {block_course_recycle} bcr
                ON
                    c.id = bcr.courseid
                WHERE
                    bcr.id IS NULL AND
                    c.enddate > 0 AND
                    c.enddate < ?
            ";
            $finishedcourses = $DB->get_records_sql($sql, [time()]);
        } else {
            $finishedcourses = [];
        }

        $assumedfinishedcourses =  [];

        if (!empty($config->policyenrols)) {

            if ($interactive) {
                mtrace("Checking by enrolments...");
            }

            // Check where all student marked user_enrolments have ended.
            $sql = "
                SELECT
                    c.id,
                    c.shortname,
                    c.enddate,
                    SUM(CASE WHEN ue.timeend = 0 OR ue.timeend >= ? THEN 1 ELSE 0 END) as actives,
                    'byenrols' AS reason
                FROM
                    {user_enrolments} ue
                JOIN
                    {enrol} e
                ON
                    e.id = ue.enrolid
                JOIN
                    {course} c
                ON
                    c.id = e.courseid
                JOIN
                    {context} ctx
                ON
                    ctx.instanceid = c.id AND
                    ctx.contextlevel = ".CONTEXT_COURSE."
                JOIN
                    {role_assignments} ra
                ON
                    ra.contextid = ctx.id
                JOIN
                    {role_capabilities} rc
                ON
                    ra.roleid = rc.roleid
                LEFT JOIN
                    {block_course_recycle} bcr
                ON
                    c.id = bcr.courseid
                WHERE
                    ra.userid = ue.userid AND
                    rc.capability = ? AND
                    (rc.contextid = ".$systemcontext->id." OR rc.contextid = ctx.id) AND
                    bcr.id IS NULL
                GROUP BY
                    c.id
                HAVING actives = 0
            ";
            $params = [time(), 'block/course_recycle:student'];

            $finishedbyenrolcourses = $DB->get_records_sql($sql, $params);

            if (!empty($finishedbyenrolcourses)) {
                foreach ($finishedbyenrolcourses as $cid => $fbec) {
                    if (!array_key_exists($cid, $assumedfinishedcourses)) {
                        $assumedfinishedcourses[$cid] = $fbec;
                    }
                }
            }
        }

        if (!empty($config->policylastaccess)) {

            if ($interactive) {
                mtrace("Checking by last_access date...");
            }

            // Check latests user_last_access
                // confirm by number of hits since X days.
            $sql = "
                SELECT
                    c.id,
                    c.shortname,
                    c.enddate,
                    ula.userid as ulau,
                    ra.userid as rau,
                    ctx.id as ctxid,
                    SUM(CASE WHEN ula.userid IS NULL THEN 0 ELSE 1 END) as actives,
                    'byaccess' AS reason
                FROM
                    mdl_course c
                LEFT JOIN
                    mdl_user_lastaccess ula
                ON
                    ula.courseid = c.id
                JOIN
                    mdl_context ctx
                ON
                    ctx.instanceid = ula.courseid AND
                    ctx.contextlevel = 50
                LEFT JOIN
                    mdl_role_assignments ra
                ON
                    ra.contextid = ctx.id
                LEFT JOIN
                    mdl_role_capabilities rc
                ON
                    ra.roleid = rc.roleid AND
                    rc.capability = ?
                LEFT JOIN
                    mdl_block_course_recycle bcr
                ON
                    c.id = bcr.courseid
                WHERE
                    1 OR
                    (ula.id IS NULL OR (ula.userid = ra.userid AND ula.timeaccess < ?)) AND
                    bcr.id IS NULL
                GROUP BY c.id
            ";

            $enddatefromnow = time() - DAYSECS * $config->mininactivedaystofinish;
            $params = ['block/course_recycle:student', $enddatefromnow, $config->minactiveaccesstomaintain];
            $finishedbyaccess = $DB->get_records_sql($sql, $params);

            if (!empty($finishedbyaccess)) {
                foreach ($finishedbyaccess as $cid => $fba) {
                    $select = ['timecreated' => $enddatefromnow, 'courseid' => $cid];
                    $logs = $DB->count_records('logstore_standard_log', $select);
                    if ($logs < $config->minhitstomaintain) {
                        if (!array_key_exists($cid, $assumedfinishedcourses)) {
                            $assumedfinishedcourses[$cid] = $fba;
                        }
                    }
                }
            }
        }

        // => Update status to "RequestForArchive".
        $nocourses = true;
        if (!empty($finishedcourses)) {
            foreach ($finishedcourses as $c) {
                $rec = new StdClass;
                $rec->courseid = $c->id;
                $rec->status = $config->defaultactionfinishedcourses;
                $rec->reason = $c->reason;
                $rec->timemodified = time();
                $rec->lastuserid = $USER->id;
                $rec->timearchived = 0;
                $rec->stopnotify = 0;
                $DB->insert_record('block_course_recycle', $rec);

                if ($interactive) {
                    mtrace("Detected course $c->id ($c->shortname) as finished. Reason was : ".$c->reason."\n");
                }

                // Guess all unique users having editing capability for notification.
                if (!empty($config->requestforarchivenotification)) {
                    // Examine all editing teachers (or first of them).
                    $courseeditingteachers = block_course_recycle_get_editingteachers($c);
                    foreach ($courseeditingteachers as $cet) {
                        if (!in_array($cet->id, $notifieduserids)) {
                            // Notify once per scan.
                            $notifieduserids[] = $cet->id;
                            $user = $DB->get_record('user', ['id' => $cet->id]);
                            if ($interactive) {
                                mtrace("Notifying user ".$cet->id.' '.fullname($user)."\n");
                            }
                            block_course_recycle_notify_requestforarchive($user);
                        }
                    }
                }
            }
            $nocourses = false;
        }

        if (!empty($assumedfinishedcourses)) {
            foreach ($assumedfinishedcourses as $c) {

                $newrec = false;
                if (!$rec = $DB->get_record('block_course_recycle', ['courseid' => $c->id])) {
                    $rec = new StdClass;
                }

                $rec->courseid = $c->id;
                $rec->status = 'RequestForArchive';
                $rec->reason = $c->reason;
                $rec->timemodified = time();
                $rec->lastuserid = $USER->id;
                $rec->timearchived = 0;
                $rec->stopnotify = 0;

                if (empty($rec->id)) {
                    $DB->insert_record('block_course_recycle', $rec);
                } else {
                    $DB->update_record('block_course_recycle', $rec);
                }

                if ($interactive) {
                    mtrace("Detected course $c->id ($c->shortname) as finished. Reason was : ".$c->reason."\n");
                }

                // Guess all unique users having editing capability for notification.
                if (!empty($config->requestforarchivenotification)) {
                    // Examine all editing teachers (or first of them).
                    $courseeditingteachers = block_course_recycle_get_editingteachers($c);
                    foreach ($courseeditingteachers as $cet) {
                        if (!in_array($cet->id, $notifieduserids)) {
                            // Notify once per scan.
                            $notifieduserids[] = $cet->id;
                            $user = $DB->get_record('user', ['id' => $cet->id]);
                            if ($interactive) {
                                mtrace("Notifying user ".$cet->id.' '.fullname($user)."\n");
                            }
                            block_course_recycle_notify_requestforarchive($user);
                        }
                    }
                }
                $nocourses = false;
            }
        }

        if (!empty($nocourses)) {
            if ($interactive) {
                mtrace("Nothing new was found.");
            }
        }
    }

    public static function process_undecided_courses($interactive) {
        global $DB;

        $config = get_config('block_course_recycle');

        // process long time waiting courses if default action is defined.
        if (!empty($config->defaultaction) && $config->decisiondelay > 0) {
            $select = " status  = ? AND timemodified >= ? ";
            $params = [ 'RequestForArchive', time() - $config->decisiondelay * DAYSECS ];
            $undecidedcourses = $DB->get_records_select('block_course_recycle', $select, $params);
            if (!empty($undecidedcourses)) {
                foreach ($undecidedcourses as $rec) {
                    // Mark the default action.
                    $rec->status = $config->defaultaction;
                    $rec->timemodified = time();
                    $DB->update_record('block_course_recycle', $rec);

                    $course = $DB->get_record('course', ['id' => $rec->courseid]);

                    // If default action has "Retire", and a retire category has been set, retire course to 
                    // hidden trashbin.
                    if (preg_match('/Retire/', $config->defaultaction)) {
                        if (!empty($config->retirecategory)) {
                            $course->category = $config->retirecategory;
                            $course->visible = 0;
                            $DB->update_record('course', $course);
                        }
                    }

                    // Send notification to owners we have decided for them.
                    // Send management panel url to let them change their mind.
                    $courseeditingteachers = block_course_recycle_get_editingteachers($course);

                    foreach ($courseeditingteachers as $cet) {
                        $user = $DB->get_record('user', ['id' => $cet->id]);
                        if ($interactive) {
                            mtrace("Notifying user ".$cet->id.' '.fullname($user)."\n");
                        }
                        block_course_recycle_notify_defaultaction($user);
                    }
                }
            }
        }
    }

    /**
     * Get all courses that might have a recycle action (status) in the enrolled
     * scope.
     */
    public static function get_my_candidate($enrolledids, $page, $pagesize, &$totalcount) {
        global $DB;

        list($insql, $inparams) = $DB->get_in_or_equal($enrolledids);

        // Get all course_recycle candidates with current status.
        $sql = "
            SELECT
                c.id,
                c.shortname,
                bcr.status
            FROM
                {course} c,
                {block_course_recycle} bcr
            WHERE
                c.id = bcr.courseid AND
                c.id $insql
        ";

        $countsql = "
            SELECT
                count(*)
            FROM
                {course} c,
                {block_course_recycle} bcr
            WHERE
                c.id = bcr.courseid AND
                c.id $insql
        ";

        $archivablecourses = $DB->get_records_sql($sql, $inparams, '', $page * $pagesize, $pagesize);
        $totalcount = $DB->count_records_sql($countsql, $inparams);

        return $archivablecourses;
    }

    /**
     * Task for the local recycling actions.
     * OBSOLETE ? 
     */
    public static function task_recycle() {
        global $DB;

        $config = get_config('block_course_recycle');

        $f = null;
        if (!empty($config->logfile)) {
            $config->logfile = str_replace('%DATAROOT%', $CFG->dataroot, $config->logfile);
            $f = fopen($logfile, 'w');
        }

        $recycles = $DB->get_records('block_instances', array('blockname' => 'course_recycle'));

        if (!empty($recycles)) {
            foreach ($recycle as $rc) {
                $blockconfig = base64_decode($rc->configdata);

                if ($blockconfig->choicedone) {
                    switch ($blockconfig) {
                        case 'throw': {
                            // Get the course :
                            $context = context::get_from_id($blockconfig->parentcontextid);
                            $course = $DB->get_record('course', array('id' => $context->instanceid));
                            // Delete.
                            if ($f) {
                                fputs($f, 'RECYCLE DELETE course '.$context->instanceid."\n");
                            }
                            course_delete_course($course, false); // Do not show feedback.
                            if ($f) {
                                fputs($f, "Deleted.\n");
                            }
                            break;
                        }

                        case 'reset': {
                            $data = new StdClass;
                            // ... TODO : Fill all resetdata subkeys. (As many as possible)
                            $data->courseid = $course->id;
                            if ($f) {
                                fputs($f, 'RECYCLE RESETTING course '.$context->instanceid."\n");
                            }
                            reset_course_userdata($data);
                            if ($f) {
                                fputs($f, "Reset.\n");
                            }
                            break;
                        }

                        case 'keep': {
                            if ($f) {
                                fputs($f, 'RECYCLE KEEPING course '.$context->instanceid."\n");
                                fputs($f, "No op.\n");
                            }
                            break;
                        }

                        case 'archive': {
                            // Activate the archive active plugin strategy.
                            if ($config->archivestrategy == 'backup') {
                                // Standard backup automation.
                            } else if ($config->archivestrategy == 'publishflow') {
                                // Pushes the course to a publishflow equiped platform.
                                include_once($CFG->dirroot.'/blocks/publishflow/xlib.php');
                                if ($f) {
                                    fputs($f, 'RECYCLE ARCHIVING course '.$context->instanceid.' with '.$config->archivestrategy."\n");
                                }
                                $whereid = $config->archivefactory;
                                $where = $DB->get_record('mnet_host', array('id' => $config->archivefactory));
                                if ($where) {
                                    block_publishflow_retrofit_course($context->instanceid, $where->wwwroot);
                                    if ($f) {
                                        fputs($f, "Backup.\n");
                                    }
                                } else {
                                    if ($f) {
                                        fputs($f, "Failed, empty or null archiving moodle.\n");
                                    }
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }

        if ($f) {
            fclose($f);
        }

    }

    /* Achive side */

    /*
     * The reverse recycling implementation relies on a source platform that prepares the identities of recyclable courses,
     * and let the archive platform operate archiving by pulling backups and restoring them in the archive environment.
     * If the recycling action needs accessory processing that follows the archiving process, a postaction status is sent
     * to the source to scedule next actions to perform after archiving such as cloning, resetting or deleting).
     * the source platform will have to perform those actions, on return, either immediately (first version), or delayed
     * as an ad_hoc task.
     */

    /**
     * Part of the reverse implementation of archiving (pulled by archive moodle)
     * Archive all (or part) of archivable courses. This runs a process that :
     * - calls the source moodle to get the list of archivable courses
     * - for each course :
     * - invokes the importer plugin to get the course restored in a target category
     * - calls the source back to acknowledge the restore and update source status.
     *
     */
    public static function task_pull_and_archive_courses() {
        global $CFG, $DB;
        global $verbose;

        $recycleconfig = get_config('block_course_recycle');

        if (is_dir($CFG->dirroot.'/blocks/import_course')) {
            include_once($CFG->dirroot.'/blocks/import_course/xlib.php');
            $pullcomponent = 'block_import_courses';
            $config = get_config('block_import_course');
            if ($verbose) {
                echo "Using import_course block for transport\n";
            }
        } else {
            if (is_dir($CFG->dirroot.'/blocks/publishflow')) {
                include_once($CFG->dirroot.'/blocks/publishflow/xlib.php');
                $pullcomponent = 'block_publishflow';
            }
            if ($verbose) {
                echo "Using publishflow block for transport\n";
            }
        }

        $archivables = course_recycler::get_archivables();

        if (!empty($archivables)) {
            foreach ($archivables as $arch) {
                // Trigger a pull.
                if ($verbose) {
                    echo "Archiving $arch->id ($arch->shortname) \n";
                }
                if ($pullcomponent == 'block_import_courses') {
                    try {
                        if (empty($recycleconfig->preservesourcecategory)) {
                            if (!$DB->record_exists('course_categories', ['id' => $config->targetcategory])) {
                                throw new \moodle_exception('Target category '.$config->targetcategory.' was not found.');
                            }
                            $targetcategoryid = $config->targetcategory;
                        } else {
                            $categorypath = $arch->sourcecategorypath;
                            $targetcategoryid = self::check_category_path($categorypath);
                        }
                        $courseid = block_import_course_import($config->teachingurl, $config->teachingtoken, $targetcategoryid, $arch->id, [], 1);
                    } catch (Exception $e) {
                        if ($verbose) {
                            echo "Archiving {$arch->id} Failed. \n";
                        }
                        debug_trace("Import of source course {$arch->id} backup has failed for some reason. Notify failed.");
                        self::notify_source($arch->id, 'Failed', $arch->status);
                        continue;
                    }

                    if ($verbose) {
                        echo "Archiving complete. \n";
                    }
                    // Notifiy.
                    self::notify_source($arch->id, 'Done', $arch->status);
                }
                // Publishflow transport is not implemented yet.
            }
        } else {
            if ($verbose) {
                echo "Nothing found to archive \n";
            }
        }
    }

    /**
     * Process some actions after an eventual remote archiving. This function is called
     * as part of the "update_course_status" webseervice call from an archiving node, but
     * can be invoked locally by local tasks when course recycling process has only local
     * actions to do.
     * @param object $course a course to process (local)
     * @param string $postaction a post action to perform
     */
    public static function process_postactions($course, $postaction) {
        // Launch a $postaction
        if (!empty($postaction)) {
            switch ($postaction) {
                // Give old status before archive to determine what needs to be done.
                case "Archive" :
                    // Nothing to do. Archive action has been processed the other side in archiving node.
                    break;
                case "Reset" :
                    // Build a "reset everything" structure and execute.
                    $data = course_recycler::build_reset_data($course);
                    reset_course_userdata($data);
                    break;
                case "CloneArchiveAndReset" :
                    // Clone the local course.
                    $newcourse = course_recycler::clone_course($course);
                    // Build a "reset everything" structure and execute.
                    if ($newcourse) {
                        $data = course_recycler::build_reset_data($newcourse);
                        reset_course_userdata($data);
                    }
                    break;
                case "ArchiveAndReset" :
                    // Build a "reset everything" structure and execute.
                    $data = course_recycler::build_reset_data($course);
                    reset_course_userdata($data);
                    break;
                case "ArchiveAndDelete" :
                    // Delete the local course. Archiving has been processed by remote node.
                    delete_course($course->id);
                    break;
            }
        }
    }

    /**
     * Get all archivable courses from a remote moodle (works on archive node only).
     */
    public static function get_archivables() {

        $config = get_config('block_course_recycle');

        $url = $config->sourcewwwroot;
        $url .= '/webservice/rest/server.php';
        $url .= '?wsfunction=block_course_recycle_get_archivable_courses';
        $url .= '&wstoken='.$config->sourcetoken;
        $url .= '&moodlewsrestformat=json';
        $url .= '&alldates=1';

        $res = curl_init($url);
        self::set_proxy($res, $url);
        curl_setopt($res, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($res, CURLOPT_POST, false);

        global $verbose;
        if ($verbose) {
            echo "Firing CURL : $url \n";
        }
        $result = curl_exec($res);
        if ($verbose) {
            if ($result) {
                echo "$result \n";
            }
        }

        // Get result content and status.
        if ($result) {
            $archivables = json_decode($result);

            if (!empty($archivables->exception)) {
                print_error($archivables->message);
            }

            if ($archivables) {
                return $archivables;
            }
        }

        return false;
    }

    /**
     * Notify source that course restore is done and ask for postactions.
     */
    public static function notify_source($courseid, $newstatus, $originalstatus) {
        global $verbose;

        $config = get_config('block_course_recycle');

        $url = $config->sourcewwwroot;
        $url .= '/webservice/rest/server.php';
        $url .= '?wsfunction=block_course_recycle_update_course_status';
        $url .= '&wstoken='.$config->sourcetoken;
        $url .= '&moodlewsrestformat=json';
        $url .= '&courseidfield=id';
        $url .= '&courseid='.$courseid;
        $url .= '&status='.$newstatus;
        $url .= '&postaction='.$originalstatus;

        $res = curl_init($url);
        self::set_proxy($res, $url);
        curl_setopt($res, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($res, CURLOPT_POST, false);

        if ($verbose) {
            echo "Firing Notify CURL : $url \n";
        }

        $result = curl_exec($res);

        // Get result content and status.
        if (!$result) {
            debug_trace("Failed notifying");
            if ($verbose) {
                echo "Failed notifying\n";
            }
        }

        return false;
    }

    protected static function set_proxy(&$res, $url) {
        global $CFG;

        // Check for proxy.
        if (!empty($CFG->proxyhost) and !is_proxybypass($url)) {
            // SOCKS supported in PHP5 only
            if (!empty($CFG->proxytype) and ($CFG->proxytype == 'SOCKS5')) {
                if (defined('CURLPROXY_SOCKS5')) {
                    curl_setopt($res, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
                } else {
                    curl_close($res);
                    print_error('socksnotsupported', 'mnet');
                }
            }

            curl_setopt($res, CURLOPT_HTTPPROXYTUNNEL, false);

            if (empty($CFG->proxyport)) {
                curl_setopt($res, CURLOPT_PROXY, $CFG->proxyhost);
            } else {
                curl_setopt($res, CURLOPT_PROXY, $CFG->proxyhost.':'.$CFG->proxyport);
            }

            if (!empty($CFG->proxyuser) and !empty($CFG->proxypassword)) {
                curl_setopt($res, CURLOPT_PROXYUSERPWD, $CFG->proxyuser.':'.$CFG->proxypassword);
                if (defined('CURLOPT_PROXYAUTH')) {
                    // any proxy authentication if PHP 5.1
                    curl_setopt($res, CURLOPT_PROXYAUTH, CURLAUTH_BASIC | CURLAUTH_NTLM);
                }
            }
        }
    }

    /**
     * Builds a reset control data structure trying to reset everything in course unless
     * editing teacher affectations.
     */
    public static function build_reset_data($course) {
        global $CFG, $DB;

        $data = [];
        $data['reset_start_date'] = 1;
        $data['reset_end_date'] = 1;

        // Processing events.
        $data['reset_events'] = 1;

        // Processing completion.
        $data['reset_completion'] = 1;

        // Processing blog associations.
        $data['delete_blog_associations'] = 1;

        // Processing logs.
        $data['reset_logs'] = 1;

        // Processing notes.
        $data['reset_notes'] = 1;

        // Processing comments.
        $data['reset_comments'] = 1;

        // Processing local role assigns and overrides.
        $data['reset_roles_local'] = 1;
        $data['reset_roles_overrides'] = 1;

        // Processing grades.
        $data['reset_gradebook_items'] = 1;
        $data['reset_gradebook_grades'] = 1;

        // Processing role assignations.
        $roles = $DB->get_records('role');
        $context = context_course::instance($course->id);
        $preserveroles = get_roles_with_capability('moodle/course:manageactivities', CAP_ALLOW, $context);
        $preserveroleids = array_keys($preserveroles);
        foreach ($roles as $role) {
            if (!in_array($role->id, $preserveroleids)) {
                $data['unenrol_users'][] = $role->id;
            }
        }

        // Processing groups.
        $data['reset_groups_remove'] = 1;
        $data['reset_groups_members'] = 1;

        // Processing groupings.
        $data['reset_groupings_remove'] = 1;
        $data['reset_groupings_members'] = 1;

        // Processing course modules.
        if ($allmods = $DB->get_records('modules') ) {
            $modmap = array();
            $modlist = array();
            $allmodsname = array();
            foreach ($allmods as $mod) {
                $modname = $mod->name;
                $allmodsname[$modname] = 1;
                if (!$DB->count_records($modname, array('course' => $data['id']))) {
                    // Skip mods with no instances.
                    continue;
                }
                $modlist[$modname] = 1;
                $modfile = $CFG->dirroot."/mod/$modname/lib.php";
                $modresetcourseformdefaults = $modname.'_reset_course_form_defaults';
                $modresetuserdata = $modname.'_reset_userdata';
                if (file_exists($modfile)) {
                    include_once($modfile);
                    if (function_exists($modresetcourseformdefaults)) {
                        /*
                         * Now we get the real internal defaults from module implementation.
                         */
                        $modmap[$modname] = $modresetcourseformdefaults($data['id']);
                    }
                } else {
                    debugging('Missing lib.php in '.$modname.' module');
                }
            }
        }

        // Scan to build mod keys.
        $availablemods = [];
        foreach ($modmap as $modname => $mod) {
            foreach ($mod as $key => $value) {
                $availablemods[$modname][$key] = $value;
            }
        }
        $data['reset_forum_all'] = 1;
        $data['reset_forum_subscriptions'] = 1;
        $data['reset_glossary_all'] = 1;
        $data['reset_chat'] = 1;
        $data['reset_data'] = 1;
        $data['reset_slots'] = 1;
        $data['reset_apointments'] = 1;
        $data['reset_assignment_submissions'] = 1;
        $data['reset_assign_submissions'] = 1;
        $data['reset_survey_answers'] = 1;
        $data['reset_lesson'] = 1;
        $data['reset_choice'] = 1;
        $data['reset_scorm'] = 1;
        $data['reset_quiz_attempts'] = 1;

        // Unsure code. does this suits to all cases and modules ? 
        // TODO : Needs deeper observation.
        foreach ($availablemods as $modname => $fcts) {
            foreach ($fcts as $fct => $value) {
                $data[$fct] = $value;
            }
        }

        $data = (object) $data;

        return $data;
    }

    /**
     * Full clone a course by backup => restore process
     * TODO : Implement backup/restore automation.
     */
    public static function clone_course($course) {
        return null;
    }

    /**
     * Scans a category path and create missing nodes in the tree if needed.
     * @param string $categorypath
     * @return int leaf category id.
     */
    public static function check_category_path($categorypath) {
        global $DB;

        $categories = explode('/', $categorypath);

        // Always starts at tree root.
        $parentcategoryid = 0;

        while ($catname = array_shift($categories)) {

            $catdata = new \Stdclass;
            $catdata->parent = $parentcategoryid;
            $catdata->name = trim($catname);

            $updated = false;
            // idnumber is the only external unique identification.
            $params = array('parent' => $parentcategoryid, 'name' => $catdata->name);
            if ($oldcat = $DB->get_record('course_categories', $params)) {
                $cat = $oldcat;
                debug_trace("Category {$oldcat->id} exists ");
            } else {
                $cat = \coursecat::create($catdata);
                if ($parentcategoryid) {
                    $parentcat = $DB->get_field('course_categories', 'name', array('id' => $parentcategoryid));
                    if (function_exists('debug_trace')) {
                        debug_trace('Category '.$catdata->name.' added to parent cat '.$parentcat);
                    }
                } else {
                    if (function_exists('debug_trace')) {
                        debug_trace('Category '.$catdata->name.' added to root cat ');
                    }
                }
            }

            // For next turn.
            $parentcategoryid = $cat->id;
        }

        // Returns the lowest leaf cat id.
        return $cat->id;
    }
}