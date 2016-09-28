<?php

require('../../../config.php');

$id = required_param('id', PARAM_INT); // Course id

if (!$course = $DB->get_record('course', array('id' => $id))) {
    die;
}

require_login($course);

$action = required_param('what', PARAM_ALPHA); // MCD command

if ($action == 'change') {
    $recycleaction = required_param('action', PARAM_ALPHA);
    $userid = required_param('userid', PARAM_INT);

    if ($oldrec = $DB->get_record('block_course_recycle', array('courseid' => $course->id)) {
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
}
if ($action == 'stopnotify') {
    $courseid = required_param('id', PARAM_INT);
    if ($oldrec = $DB->get_record('block_course_recycle', array('courseid' => $course->id)) {
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
    // Stop notifications for this user 
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
    // Restore notifications for this user
    $userid = required_param('userid', PARAM_INT);

    $DB->delete_records('user_preferences', array('userid' => $userid, 'name' => 'recycle_notify_stop'));
}