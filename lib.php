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

require_once($CFG->dirroot.'/blocks/course_recycle/mailtemplatelib.php');

define('RECYCLE_RQFA', 'RequestForArchive');
define('RECYCLE_STAY', 'Stay');
define('RECYCLE_RESET', 'Reset');
define('RECYCLE_CLONE', 'Clone');
define('RECYCLE_CLONETANDRESET', 'CloneAndReset');
define('RECYCLE_ARCHIVE', 'Archive');
define('RECYCLE_CLONEARCHIVEANDRESET', 'CloneArchiveAndReset');
define('RECYCLE_DELETE', 'Delete');
define('RECYCLE_DONE', 'Done'); // This is an extra
define('RECYCLE_FAILED', 'Failed'); // This is an extra

/**
 * This function is not implemented in this plugin, but is needed to mark
 * the vf documentation custom volume availability.
 * 
 * @param string $feature a cat/item feature key.
 */
function block_course_recycle_supports_feature($feature) {
    assert(1);
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

    ticket_notify($user, $admin, $title, $notification, $notificationhtml, $returnurl, 'Recycler', 'long');

    return true;

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