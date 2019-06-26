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

require_once($CFG->dirroot.'/blocks/course_recycle/lib.php');

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
        global $DB, $USER;

        $config = get_config('block_course_recycle');

        $finished = [];
        $notifieduserids = [];

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

        if ($interactive) {
            mtrace("Checking by enrolments...");
        }

        // Check all user_enrolments have ended
        $sql = "
            SELECT
                c.id,
                c.shortname,
                c.enddate,
                SUM(CASE WHEN ue.timeend = 0 OR ue.timeend >= ? THEN 1 ELSE 0 END) as actives,
                'byenrols' AS reason
            FROM
                {user_enrolments} ue,
                {enrol} e,
                {course} c
            LEFT JOIN
                {block_course_recycle} bcr
            ON
                c.id = bcr.courseid
            WHERE
                c.id = e.courseid AND
                e.id = ue.enrolid AND
                bcr.id IS NULL
            GROUP BY
                c.id
            HAVING actives = 0
        ";
        $finishedbyenrolcourses = $DB->get_records_sql($sql, [time()]);

        if (!empty($finishedbyenrolcourses)) {
            foreach ($finishedbyenrolcourses as $cid => $fbec) {
                if (!array_key_exists($cid, $finishedcourses)) {
                    $finishedcourses[$cid] = $fbec;
                }
            }
        }

        if ($interactive) {
            mtrace("Checking by access log...");
        }

        // Check latests user_last_access
            // confirm by number of hits since X days.
        $sql = "
            SELECT
                c.id,
                c.shortname,
                c.enddate,
                SUM(1) as actives,
                'byaccess' AS reason
            FROM
                {user_lastaccess} ula,
                {course} c
            LEFT JOIN
                {block_course_recycle} bcr
            ON
                c.id = bcr.courseid
            WHERE
                ula.courseid = c.id AND
                ula.timeaccess > ? AND
                bcr.id IS NULL
            HAVING
                actives <= ?
        ";
        $enddatefromnow = time() - DAYSECS * $config->mininactivedaystofinish;
        $finishedbyaccess = $DB->get_records_sql($sql, [$enddatefromnow, $config->minactiveaccesstomaintain]);

        if (!empty($finishedbyaccess)) {
            foreach ($finishedbyaccess as $cid => $fba) {
                $select = ['timecreated' > $enddatefromnow, 'courseid' => $cid];
                $logs = $DB->count_records('logstore_standard_log', $select);
                if ($logs < $config->minhitstomaintain) {
                    if (!array_key_exists($cid, $finishedcourses)) {
                        $finishedcourses[$cid] = $fba;
                    }
                }
            }
        }

        // => Update status to "RequestForArchive".
        if (!empty($finishedcourses)) {
            foreach ($finishedcourses as $c) {
                $rec = new StdClass;
                $rec->courseid = $c->id;
                $rec->status = 'RequestForArchive';
                $rec->reason = $c->reason;
                $rec->timemodified = time();
                $rec->lastuserid = $USER->id;
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
        } else {
            if ($interactive) {
                mtrace("Nothing new was found.");
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
        global $CFG;

        if (is_dir($CFG->dirroot.'/blocks/import_courses')) {
            include_once($CFG->dirroot.'/blocks/import_courses/xlib.php');
            $pullcomponent = 'block_import_courses';
        } else {
            if (is_dir($CFG->dirroot.'/blocks/publishflow')) {
                include_once($CFG->dirroot.'/blocks/publishflow/xlib.php');
                $pullcomponent = 'block_publishflow';
            }
        }

        $archivables = course_recycler::get_archivables();

        if (!empty($archivables)) {
            foreach ($archivable as $arch) {
                // Trigger a pull.
                if ($pullcomponent == 'block_import_courses') {
                    try {
                        $courseid = block_import_course_import($arch->id);
                    } catch (Exception $e) {
                        debug_trace("Import of source course {$arch->id} backup has failed for some reason. Notify failed.");
                        self::notify_source($arch->id, 'Failed', $arch->status);
                        continue;
                    }

                    // Notifiy.
                    self::notify_source($arch->id, 'Done', $arch->status);
                }
            }
        }
    }

    public static function get_archivables() {

        $config = get_config('block_course_recycle');

        $url = $config->sourcewwwroot;
        $url .= '/webservice/rest/simpleserver.php';
        $url .= '?wsfunction=block_course_recycle_get_archivable_courses';
        $url .= '&wstoken='.$config->sourcetoken;

        $res = curl_init($url);
        self::set_proxy($res, $url);
        curl_setopt($res, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($res, CURLOPT_POST, false);

        $result = curl_exec($res);

        // Get result content and status.
        if ($result) {
            $archivables = json_decode($result);
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

        $config = get_config('block_course_recycle');

        $url = $config->sourcewwwroot;
        $url .= '/webservice/rest/simpleserver.php';
        $url .= '?wsfunction=block_course_recycle_update_course_status';
        $url .= '&wstoken='.$config->sourcetoken;
        $url .= '&courseidfield=id';
        $url .= '&courseid='.$courseid;
        $url .= '&status='.$newstatus;
        $url .= '&postactions='.$originalstatus;

        $res = curl_init($url);
        self::set_proxy($res, $url);
        curl_setopt($res, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($res, CURLOPT_POST, false);

        $result = curl_exec($res);

        // Get result content and status.
        if (!$result) {
            debug_trace("Failed notifying");
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
                    print_error('socksnotsupported','mnet');
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
}