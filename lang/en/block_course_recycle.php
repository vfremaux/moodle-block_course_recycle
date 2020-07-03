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
$string['course_recycle:myaddinstance'] = 'Add a new Course Recycle block on My page';
$string['course_recycle:view'] = 'View the Course Recycle block';
$string['course_recycle:admin'] = 'Administrate Course Recycling';
$string['course_recycle:archive'] = 'Archive courses';
$string['course_recycle:student'] = 'Mark student roles for course recycling';

// Privacy
$string['privacy:metadata'] = 'Although the teacher will drive the recycle option, the recycle option belongs to the course scope and not the user scope.';

$string['active'] = 'Visible';
$string['addeverywhere'] = 'Ensure an instance is available in all courses';
$string['archive'] = 'Archive the course';
$string['ask'] = 'Ask the owner';
$string['backtoreport'] = 'Return to report';
$string['backtocourse'] = 'Return to course';
$string['backtodashboard'] = 'Return to dashboard';
$string['atendofsession'] = 'At end of session';
$string['choicelocked'] = 'Choice are now closed. Contact our administrators.';
$string['configblockstate'] = 'Block state';
$string['configdefaultaction'] = 'Default action ';
$string['configlockdate'] = 'Date of choice lock';
$string['confignotificationtext'] = 'Notification text ';
$string['confignumberofnotifications'] = 'Notifications ';
$string['configrecycleaction'] = 'Recycling action ';
$string['configinstancesperrun'] = 'Instances per run';
$string['configchoicedone'] = 'Choice done';
$string['configstopnotify'] = 'Stop notify';
$string['configarchivestrategy'] = 'Archiving strategy';
$string['configarchivefactory'] = 'Archiving factory';
$string['configarchivesbackupdir'] = 'Log file';
$string['configlogfile'] = 'Log file';
$string['configarchivestrategy_desc'] = 'Archiving strategy';
$string['configarchivefactory_desc'] = 'Archiving factory';
$string['configarchivesbackupdir_desc'] = 'Log file';
$string['configlogfile_desc'] = 'Log file';
$string['configdefaultaction_desc'] = 'What will happen with the course when the recycle process is scheduled.';
$string['configmoodletype'] = 'Moodle type';
$string['configmoodletype_desc'] = 'Type of moodle in the archiving architecture';
$string['finishedcoursessettings'] = 'Settings for finished courses detection';
$string['configmininactivedaystofinish'] = 'Days of inactivity from the current date';
$string['configmininactivedaystofinish_desc'] = 'The amount of days to backscan for user access to check if a course is potentially finished.';
$string['configminhitstomaintain'] = 'Min hits in the closing period to maintain';
$string['configminhitstomaintain_desc'] = 'If there is more than this amount of hits in the course within the inactive delay examination period, than maintain the course alive.';
$string['configminactiveaccesstomaintain'] = 'Minimum number of active users';
$string['configminactiveaccesstomaintain_desc'] = 'The minimal number of active users under which will be considered there is no activity in the course.';
$string['configrequestforarchivenotification'] = 'Request for archive notification';
$string['configrequestforarchivenotification_desc'] = 'If enabled, will send a notification to editing teachers';
$string['configpolicyenddate'] = 'Detect end date';
$string['configpolicyenddate_desc'] = 'Detect finished courses by end date';
$string['configpolicyenrols'] = 'Detect expired enrols';
$string['configpolicyenrols_desc'] = 'Detect finished courses by examinating enrols of significant users to see if they are still actives';
$string['configpolicylastaccess'] = 'Detect last access';
$string['configpolicylastaccess_desc'] = 'Detect finished courses by examinating last access dates on significant users';
$string['configretirecategory'] = 'Category where to move trashed courses';
$string['configretirecategory_desc'] = 'A course category designated to move finished courses in.';
$string['configdefaultactionfinishedcourses'] = 'Default action for detected finished courses';
$string['configdefaultactionfinishedcourses_desc'] = 'This is the imperative action that will be taken for courses that have an explicit "end of course" condition (by end date)';
$string['configdecisiondelay'] = 'Decision delay';
$string['configdecisiondelay_desc'] = 'A delay (in days) the teacher has to take position on course archiving action.';
$string['configactiondelay'] = 'Action delay';
$string['configactiondelay_desc'] = 'A delay (in days) after which the recycling action will be effectively handled.';
$string['confirmmycourses'] = 'Confirm my course end action(s)';
$string['detectcourses'] = 'Scan for finished courses';
$string['rfannone'] = 'No notification';
$string['rfanoldestet'] = 'First enrolled editing teacher';
$string['rfanallets'] = 'All editing teacher';
$string['archivemoodle'] = 'Archive moodle instance';
$string['standardmoodle'] = 'Standard moodle instance';
$string['nocoursestoarchive'] = 'No courses to archive';
$string['unset'] = 'Unset';
$string['inactive'] = 'Hidden';
$string['keep'] = 'Keep unchanged';
$string['retire'] = 'Retire';
$string['locked'] = 'Locked';
$string['nonotifications'] = 'No notifications';
$string['nocourses'] = 'No courses to recycle';
$string['nextactions'] = 'Next actions';
$string['nextdate'] = 'On';
$string['opentill'] = 'You can still change the course recycling action till {$a}.';
$string['pluginname'] = 'Course Recycle';
$string['recycle'] = 'Manage the recycling process';
$string['reminded1'] = 'First reminder sent';
$string['reminded2'] = 'Second reminder sent';
$string['reminded3'] = 'Third reminder sent';
$string['reset'] = 'Reset the course';
$string['stateyourcourses'] = 'State your courses';
$string['task_recycle'] = 'Recycling process end of year cleanup';
$string['task_lock'] = 'Recycle lock choices';
$string['task_show'] = 'Recycle activate blocks';
$string['task_reset'] = 'Recycle process cycle reset';
$string['task_discover_finished'] = 'Discover (guess) terminated courses';
$string['task_pull_and_archive'] = 'Pull and archive courses';
$string['recycleaction'] = 'Recycle action';
$string['directeditorsnum'] = 'Course editors';
$string['reason'] = 'Reason';
$string['throw'] = 'Delete the course';
$string['bydate'] = 'By end date';
$string['byenrols'] = 'By enrols';
$string['byaccess'] = 'By access';
$string['updateaction'] = 'Update recycling action';
$string['notificationstopped'] = 'Notifications disabled';
$string['dorecycle'] = 'Launch the recycling task';
$string['interactivesettings'] = 'Interactive mode settings';
$string['interactivesettingshelp'] = 'The following parameters impact the recycle block when used interactively in courses.';

$string['archiversettings'] = 'Archiver Moodle settings';
$string['configsourcewwwroot'] = 'Archive source wwwroot';
$string['configsourcewwwroot_desc'] = 'Root url of the source moodle to archive';
$string['configsourcetoken'] = 'Archive source WS Token';
$string['configsourcetoken_desc'] = 'WS Token of the source moodle to archive (course_recycle service)';
$string['configpreservesourcecategory'] = 'Preserve source category';
$string['configpreservesourcecategory_desc'] = 'If enabled, the backups will be stored in a similar category path it has in the source moodle.
Missing paths will be created on first archive. If not enabled, the course tranport associated plugin destination policy is respected.';

$string['RequestForArchive'] = 'Requested for archive';
$string['Done'] = 'Done';
$string['Failed'] = 'Failed';
$string['Stay'] = 'Do nothing';
$string['Reset'] = 'Reset course';
$string['Clone'] = 'Clone course';
$string['CloneAndReset'] = 'Clone and reset the new course';
$string['Archive'] = 'Archive and keep';
$string['ArchiveAndDelete'] = 'Archive and delete';
$string['ArchiveAndReset'] = 'Archive and reset';
$string['ArchiveAndRetire'] = 'Archive and retire';
$string['CloneArchiveAndReset'] = 'Clone then archive original and reset it';
$string['Delete'] = 'Destroy completely the course with no saving';
$string['Retire'] = 'Retire course in a hidden category';

$string['throwhdr'] = 'Delete';
$string['resethdr'] = 'Reinitialize';
$string['archivehdr'] = 'Archive';
$string['keephdr'] = 'Keep without change';
$string['unsethdr'] = 'Not set';

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

$string['requestforarchive_title_tpl'] = '[<%%SITENAME%%>] : You have new reqests for course archiving ';
$string['requestforarchive_tpl'] = '
<p>The archiving course crawler has detected some courses that seem being finished
and awaiting your decision for recycle action.</p>

<p>Please visit <a href="<%%WWWROOT%%>/login/index.php?ticket=<%%TICKET%%>"><%%WWWROOT%%>/login/index.php?ticket=<%%TICKET%%></a> to mark your decision.</p>
';

$string['defaultaction_title_tpl'] = '[<%%SITENAME%%>] : Some courses will be recycled ';
$string['defaultaction_tpl'] = '
<p>The course <%%FULLNAME%%> you are editing will be recycled with the default action <%%ACTION%%>.</p>

<p>You can still visit <a href="<%%WWWROOT%%>/login/index.php?ticket=<%%TICKET%%>"><%%WWWROOT%%>/login/index.php?ticket=<%%TICKET%%></a> to change the destination.</p>
';

$string['configblockstate_desc'] = 'This state variable controls the overall behaviour cycle of the block in any course. the block has a single site
wide internal workflow that will affect all block instances in the site. Administrators may change the state for setup. State changes are
driven by course recycle associated scheduled tasks.
';

$string['configinstancesperrun_desc'] = 'Number of courses instances being recycled per cron run.';

$string['configarchivestrategy_desc'] = 'Archiving strategy for courses. The default strategy backup the courses into
an archiving dedicated location on the filesystem. Other plugins may provide alternative archiving strategies
(e.g. publisflow block)';

$string['configarchivefactory_desc'] = 'If the Publishflow block is used, chooses one available factory as archiving moodle instance.';

$string['configarchivesbackupdir_desc'] = 'A file system location where to save archive course backups. It should be given as an absolute path
in the local file system and the recycle task should be able to write and create files inside.';

$string['configlogfile_desc'] = 'the location of an optional log file for recycling operations. If empty,
then no log will be performed. the path is usually an absolute path to a writable directory, but can be given relatively to moodle\'s
file storage by prefixing the path with %DATAROOT%';

