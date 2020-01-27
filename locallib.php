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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/blocks/course_recycle/lib.php');

/**
 * This is a relocalized function in order to get local_my more compact.
 * checks if a user has a some named capability effective somewhere in a course.
 * @param string $capability;
 * @param bool $excludesystem
 * @param bool $excludesite
 * @param bool $doanything
 * @param string $contextlevels restrict to some contextlevel may speedup the query.
 */
function block_course_recycle_has_capability_somewhere($capability, $excludesystem = true, $excludesite = true,
                                           $doanything = false, $contextlevels = '', $checkvisible = false) {
    global $USER, $DB;

    if (empty($contextlevels)) {
        $contextlevels = [CONTEXT_COURSE, CONTEXT_COURSECAT];
    } else {
        $contextlevels = explode(',', $contextlevels);
    }

    $params['capability'] = $capability;
    $params['userid'] = $USER->id;

    if (in_array(CONTEXT_COURSE, $contextlevels)) {

        $sitecontextexclclause = '';
        if ($excludesite) {
            $sitecontextexclclause = " ctx.id != 1  AND ";
        }

        $coursecheckvisibleclause = '';
        if ($checkvisible) {
            $coursecheckvisibleclause = " c.visible = 1 AND ";
        }

        // This is a a quick rough query that may not handle all role override possibility.

        $sql = "
            SELECT
                COUNT(DISTINCT ra.id)
            FROM
                {role_capabilities} rc,
                {role_assignments} ra,
                {context} ctx,
                {course} c
            WHERE
                rc.roleid = ra.roleid AND
                ra.contextid = ctx.id AND
                $sitecontextexclclause
                rc.capability = :capability AND
                ctx.contextlevel = ".CONTEXT_COURSE." AND
                ctx.instanceid = c.id AND
                $coursecheckvisibleclause
                ra.userid = :userid AND
                rc.permission = 1
        ";
        $hassomecourses = $DB->count_records_sql($sql, $params);
    }

   if (in_array(CONTEXT_COURSECAT, $contextlevels)) {

        $sitecontextexclclause = '';
        if ($excludesite) {
            $sitecontextexclclause = " ctx.id != 1  AND ";
        }

        $coursecatcheckvisibleclause = '';
        if ($checkvisible) {
            $coursecatcheckvisibleclause = " cc.visible = 1 AND ";
        }

        // This is a a quick rough query that may not handle all role override possibility.

        $sql = "
            SELECT
                COUNT(DISTINCT ra.id)
            FROM
                {role_capabilities} rc,
                {role_assignments} ra,
                {context} ctx,
                {course_categories} cc
            WHERE
                rc.roleid = ra.roleid AND
                ra.contextid = ctx.id AND
                $sitecontextexclclause
                rc.capability = :capability AND
                ctx.contextlevel = ".CONTEXT_COURSECAT." AND
                ctx.instanceid = cc.id AND
                $coursecatcheckvisibleclause
                ra.userid = :userid AND
                rc.permission = 1
        ";
        $hassomecategories = $DB->count_records_sql($sql, $params);
    }

    if (!empty($hassomecourses) || !empty($hassomecategories)) {
        return true;
    }

    $systemcontext = context_system::instance();
    if (!$excludesystem && has_capability($capability, $systemcontext, $USER->id, $doanything)) {
        return true;
    }

    return false;
}

/**
 * This function clones the accesslib.php function get_user_capability_course, and gets the list
 * of courses that this user has a particular capability in. the difference resides in that we look
 * only for direct assignations here and not on propagated authorisations.
 * It is still not very efficient.
 *
 * @param string $capability Capability in question
 * @param int $userid User ID or null for current user
 * @param bool $doanything True if 'doanything' is permitted (default)
 * @param string $fieldsexceptid Leave blank if you only need 'id' in the course records;
 *   otherwise use a comma-separated list of the fields you require, not including id
 * @param string $orderby If set, use a comma-separated list of fields from course
 *   table with sql modifiers (DESC) if needed
 * @return array|bool Array of courses, if none found false is returned.
 */
function block_course_recycle_get_user_capability_course($capability, $userid = null, $doanything = true, $fieldsexceptid = '',
                                          $orderby = '') {
    global $DB, $CFG;

    $debug = optional_param('debug', false, PARAM_BOOL) && ($CFG->debug >= DEBUG_ALL);

    // Convert fields list and ordering.
    $fieldlist = '';
    if ($fieldsexceptid) {
        $fields = explode(',', $fieldsexceptid);
        foreach ($fields as $field) {
            $fieldlist .= ',c.'.$field;
        }
    }
    if ($orderby) {
        $fields = explode(',', $orderby);
        $orderby = '';
        foreach ($fields as $field) {
            if ($orderby) {
                $orderby .= ',';
            }
            $orderby .= $field;
        }
        $orderby = 'ORDER BY '.$orderby;
    }

    /* Obtain a list of everything relevant about all courses including context but
     * only where user has roles directly inside.
     * Note the result can be used directly as a context (we are going to), the course
     * fields are just appended.
     */

    $contextpreload = context_helper::get_preload_record_columns_sql('x');

    $courses = array();

    $sql = "
        SELECT
            c.id
            $fieldlist,
            $contextpreload
        FROM
            {course} c
        JOIN
            {context} x
        ON
            (c.id=x.instanceid AND x.contextlevel=".CONTEXT_COURSE.")
        JOIN
            {role_assignments} ra
        ON
            (ra.contextid = x.id AND ra.userid = ?)
        JOIN
            {course_categories} cc
        ON
            cc.id = c.category
        GROUP BY
            c.id

        $orderby";

    $rs = $DB->get_recordset_sql($sql, array($userid));

    // Check capability for each course in turn.
    foreach ($rs as $course) {
        $context = context_course::instance($course->id);
        if (has_capability($capability, $context, $userid, $doanything)) {
            /*
             * We've got the capability. Make the record look like a course record
             * and store it
             */
            $courses[$course->id] = $course;
            if ($debug) {
                echo "Catched {$course->id} by query on $capability<br/>\n";
            }
        } else {
            if ($debug) {
                echo "Rejected {$course->id} by capability $capability<br/>\n";
            }
        }
    }
    $rs->close();
    return $courses;
}

/**
 * Get all users with moodle/course:manageactivities capability in course (whatever role that direct assign provides).
 * @param object $course
 */
function block_course_recycle_get_editingteachers($course) {
    global $DB;

    $config = get_config('block_course_recycle');

    $context = context_course::instance($course->id);
    $rcs = get_roles_with_cap_in_context($context, 'moodle/course:manageactivities');

    if (empty($rcs)) {
        return [];
    }

    // Get the earlyiest possible assigns per userid and course. This will give us the list of editing teachers classed
    // By entry date based on enrol. Non enrolled editors might not be retained.

    $editingusers = [];
    $roleids = [];
    foreach ($rcs as $rc) {
        if (!array_key_exists($rc->roleid, $roles)) {
            $roles[$rc->roleid] = $DB->get_record('role', array('id' => $rc->roleid));
        }
    }

    $sql = "
        SELECT
            ra.*,
            ctx.instanceid as courseid,
            MIN(ue.timecreated) as minenrol
        FROM
            {role_assignments} ra
            {context} ctx,
            {enrol} e,
            {user_enrolments} ue
        WHERE
            e.id = ue.enrolid AND
            ra.contextid = ctx.id AND
            ctx.contextlevel = 50 AND
            e.courseid = ctx.intanceid AND
            ra.contextid = ? AND
            ra.roleid = ?
        GROUP BY
            ra.userid, ctx.instanceid
        ORDER BY
            ue.timecreated
    ";
    $enrolledassigns = $DB->get_records_sql($sql,
                            array($context->id, $roles[$rc->roleid]));

    if (empty($enrolledassigns)) {
        return [];
    }

    $courses = [];
    foreach ($enrolledassigns as $ea) {
        if ($config->requestforarchivenotification == 2) {
            if (!in_array($ea->userid, $assigns)) {
                // Ensure they are unique but this should be already true.
                $editingusers[] = $ea->userid;
            }
        } else if ($config->requestforarchivenotification == 2) {
            if (!in_array($ea->courseid)) {
                if (!in_array($ea->userid, $assigns)) {
                    // Take the earliest per course.
                    // Ensure they are unique but this should be already true.
                    $editingusers[] = $ea->userid;
                    $courses[] = $ea->courseid;
                }
            }
        }
    }
    return $editingusers;
}

/**
 * Send one request per turn per user if there are some courses to state for.
 * Users may be stripped of the notification list if course "stopnotify" attribute us set
 * in recycling record.
 *
 * @param object $user
 */
function block_course_recycle_notify_requestforarchive($user) {
    global $CFG, $SITE, $DB;

    $vars = array(
        'SITENAME' => $SITE->fullname,
        'WWWROOT' => $CFG->wwwroot
    );

    $notificationhtml = block_course_recycle_compile_mail_template('requestforarchive_tpl', $vars, $user->lang);
    $notification = strip_tags($notificationhtml);
    $title = block_course_recycle_compile_mail_template('requestforarchive_title_tpl', $vars, $user->lang);

    $admin = get_admin();

    $returnurl = new moodle_url('/blocks/course_recycle/confirmmycourses.php');

    ticket_notify($user, $admin, $title, $notification, $notificationhtml, $returnurl->out(), 'Recycler', 'long');

    return true;

}

/**
 * Send one request per turn per user if there are some courses to state for.
 * Users may be stripped of the notification list if course "stopnotify" attribute us set
 * in recycling record.
 *
 * @param object $user
 */
function block_course_recycle_notify_defaultaction($user, $course) {
    global $CFG, $SITE, $DB;

    $config = get_config('block_course_recycle');

    $vars = array(
        'SITENAME' => $SITE->fullname,
        'WWWROOT' => $CFG->wwwroot,
        'FULLNAME' => $course->fullname,
        'SHORTNAME' => $course->shortname,
        'ACTION' => get_string($config->defaultaction, 'block_course_recycle')
    );

    $notificationhtml = block_course_recycle_compile_mail_template('defaultaction_tpl', $vars, $user->lang);
    $notification = strip_tags($notificationhtml);
    $title = block_course_recycle_compile_mail_template('defaultaction_title_tpl', $vars, $user->lang);

    $admin = get_admin();

    $returnurl = new moodle_url('/blocks/course_recycle/confirmmycourses.php');

    ticket_notify($user, $admin, $title, $notification, $notificationhtml, $returnurl->out(), 'Recycler', 'long');

    return true;
}
