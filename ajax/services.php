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
define('AJAX_SCRIPT');

require('../../../config.php');

$id = required_param('id', PARAM_INT); // Course id.

if (!$course = $DB->get_record('course', array('id' => $id))) {
    die;
}

$context = context_course::instance($course->id);

require_login($course);

$action = required_param('what', PARAM_ALPHA); // MCD command.

if ($action == 'change') {
    $recycleaction = required_param('action', PARAM_ALPHA);
    $userid = required_param('userid', PARAM_INT);

    $PAGE->set_context($context);
    $renderer = $PAGE->get_renderer('block_course_recycle');

    if ($oldrec = $DB->get_record('block_course_recycle', array('courseid' => $course->id))) {
        $oldrec->userid = $userid;
        $oldrec->recycleaction = $recycleaction;
        $DB->update_record('block_course_recycle', $oldrec);
    } else {
        $rec = new StdClass;
        $rec->userid = $userid;
        $rec->courseid = $course->id;
        $rec->recycleaction = $recycleaction;
        $DB->insert_record('block_course_recycle', $rec);
    }
    echo $renderer->recyclebutton($recycleaction);
    die;
}
if ($action == 'stopnotify') {
    $courseid = required_param('id', PARAM_INT);
    if ($oldrec = $DB->get_record('block_course_recycle', array('courseid' => $course->id))) {
        $oldrec->stopnotify = true;
        $DB->update_record('block_course_recycle', $oldrec);
    } else {
        $rec = new StdClass;
        $rec->userid = $userid;
        $rec->courseid = $courseid;
        $rec->recycleaction = $recycleaction;
        $oldrec->stopnotify = true;
        $DB->insert_record('block_course_recycle', $rec);
    }
}
if ($action == 'stopnotifyall') {
    // Stop notifications for this user.
    $userid = required_param('userid', PARAM_INT);

    if ($oldrec = $DB->get_record('user_preferences', array('userid' => $userid, 'name' => 'recycle_notify_stop'))) {
        $DB->set_field('user_preferences', 'value', 0, array('userid' => $userid, 'name' => 'recycle_notify_stop'));
    } else {
        $rec = new Stdclass;
        $rec->userid = $userid;
        $rec->name = 'recycle_notify_stop';
        $rec->value = 1;
        $DB->insert_record('user_preferences', $rec);
    }
}
if ($action == 'restorenotifyall') {
    // Restore notifications for this user.
    $userid = required_param('userid', PARAM_INT);

    $DB->delete_records('user_preferences', array('userid' => $userid, 'name' => 'recycle_notify_stop'));
}