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
 * Strings for component 'block_html', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   block_course_recycle
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['course_recycle:addinstance'] = 'Add a new Course Recycle block';
$string['course_recycle:view'] = 'View the Course Recycle block';
$string['course_recycle:admin'] = 'Administrate Course Recycling';

$string['atendofsession'] = 'At end of session';
$string['throw'] = 'At end of session, delete the course';
$string['reset'] = 'At end of session, reset the course';
$string['keep'] = 'At end of session, keep unchanged';
$string['pluginname'] = 'Course Recycle';
$string['nonotifications'] = 'No notifications';

$string['defaultnotification_title_tpl'] = 'Course reycling notice %NOTIF% : %COURSE%';
$string['defaultnotification_tpl'] = 'This course will be recycled on %TASKDATE%. You can choose which action
will be performed on this course at this date. If you do NOT choose, your course will be %DEFAULTACTION%. 

Browse <a href="%COURSEURL%">in your course</a> to choose the course action or to stop notifications.
';

$string['task_recycle'] = 'Recycling end of life courses';

$string['configresetdate'] = 'Date for reset';
$string['configresetdate_desc'] = 'The date the block will be reset and will not be shown any more in the courses';

$string['configshowdate'] = 'Date for showing';
$string['configshowdate_desc'] = 'The date the block will be shown if present in the course. The block will be shown untill the reset date is reached';

$string['configrecycleaction'] = 'Recycling action ';

$string['configdefaultaction'] = 'Default action ';
$string['configdefaultaction_desc'] = 'What will happen with the course when the recycle process is scheduled.';

$string['confignumberofnotifications'] = 'Notifications ';
$string['confignumberofnotifications_desc'] = 'The amount of messages that will be sent to the course editing teachers before the task scheduling date. Notifications will be sent at 15 days interval.';

$string['confignotificationtext'] = 'Notification text ';
$string['confignotificationtext_desc'] = 'The text to be sent. this text can use %NOTIF% as the notification number and %REMAININGNOTIFS% for the remaining amount of notifications before processing. %TASKDATE% can be used as processing task date information.';
