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

$string['course_recycle:addinstance'] = 'Add a new Course Recycle block';
$string['course_recycle:view'] = 'View the Course Recycle block';
$string['course_recycle:admin'] = 'Administrate Course Recycling';

$string['active'] = 'Visible';
$string['addeverywhere'] = 'Ensure an instance is available in all courses';
$string['archive'] = 'At end of session, archive the course';
$string['atendofsession'] = 'At end of session';
$string['choicelocked'] = 'Choice are now closed. Contact our administrators.';
$string['configblockstate'] = 'Block state';
$string['configdefaultaction'] = 'Default action ';
$string['configdefaultaction_desc'] = 'What will happen with the course when the recycle process is scheduled.';
$string['configlockdate'] = 'Date of choice lock';
$string['confignotificationtext'] = 'Notification text ';
$string['confignumberofnotifications'] = 'Notifications ';
$string['configrecycleaction'] = 'Recycling action ';
$string['inactive'] = 'Hidden';
$string['keep'] = 'At end of session, keep unchanged';
$string['locked'] = 'Locked';
$string['nonotifications'] = 'No notifications';
$string['opentill'] = 'You can still change the course recycling action till {$a}.';
$string['pluginname'] = 'Course Recycle';
$string['recycle'] = 'Manage the recycling process';
$string['reminded1'] = 'First reminder sent';
$string['reminded2'] = 'Second reminder sent';
$string['reminded3'] = 'Third reminder sent';
$string['reset'] = 'At end of session, reset the course';
$string['task_recycle'] = 'Recycling process end of year cleanup';
$string['task_lock'] = 'Recycle lock choices';
$string['task_show'] = 'Recycle activate blocks';
$string['task_reset'] = 'Recycle process cycle reset';
$string['throw'] = 'At end of session, delete the course';

$string['confignumberofnotifications_desc'] = 'The amount of messages that will be sent to the course editing teachers
before the task scheduling date. Notifications will be sent at 15 days interval.';

$string['confignotificationtext_desc'] = 'The text to be sent. this text can use %NOTIF% as the notification number
and %REMAININGNOTIFS% for the remaining amount of notifications before processing. %TASKDATE% can be used as processing
task date information.';

$string['defaultnotification_title_tpl'] = 'Course reycling notice %NOTIF% : %COURSE%';
$string['defaultnotification_tpl'] = 'This course will be recycled on %TASKDATE%. You can choose which action
will be performed on this course at this date. If you do NOT choose, your course will be %DEFAULTACTION%.

Browse <a href="%COURSEURL%">in your course</a> to choose the course action or to stop notifications.
';

$string['configblockstate_desc'] = 'This state variable controls the overall behaviour cycle of the block in any course. the block has a single site
wide internal workflow that will affect all block instances in the site. Administrators may change the state for setup. State changes are
driven by course recycle associated scheduled tasks.
';