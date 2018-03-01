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
define('AJAX_SCRIPT', 1);

require('../../../config.php');

$id = required_param('id', PARAM_INT); // Block id.
$course = required_param('course', PARAM_INT); // Course id.

if (!$course = $DB->get_record('course', array('id' => $course))) {
    die("No course");
}

if (!$blockrec = $DB->get_record('block_instances', array('id' => $id))) {
    die("No block");
}

$context = context_block::instance($id);
$coursecontext = context_course::instance($course->id);

require_login($course);

$action = required_param('what', PARAM_ALPHA); // MCD command.

if ($action == 'change') {
    $recycleaction = required_param('state', PARAM_ALPHA);

    $PAGE->set_context($coursecontext);
    $renderer = $PAGE->get_renderer('block_course_recycle');

    $block = block_instance('course_recycle', $blockrec);
    $block->config->recycleaction = $recycleaction;
    $block->config->choicedone = true;
    $block->instance_config_save($block->config);

    echo $renderer->recyclebutton($recycleaction, $id);
    die;
}
if ($action == 'stopnotify') {
    $block = block_instance('course_recycle', $blockrec);
    $block->config->stopnotify = true;
    $block->instance_config_save($block->config);
    die;
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