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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/blocks/course_recycle/adminlib.php');

use \block\course_recycle\admin_setting_configdatetime;

if ($ADMIN->fulltree) {

    $year = date('Y', time());

    $defaultreset = array('y' => $year, 'M' => 8, 'd' => 15, 'h' => 0, 'm' => 0);

    $key = 'block_course_recycle/resetdate';
    $label = get_string('configresetdate', 'block_course_recycle');
    $desc = get_string('configresetdate_desc', 'block_course_recycle');
    $settings->add(new admin_setting_configdatetime($key, $label,
                       $desc, $defaultreset, array('tmask' => true, 'ymask' => true)));

    $defaultset = array('y' => $year, 'M' => 5, 'd' => 15, 'h' => 0, 'm' => 0);
    $key = 'block_course_recycle/showdate';
    $label = get_string('configshowdate', 'block_course_recycle');
    $desc = get_string('configshowdate_desc', 'block_course_recycle');
    $settings->add(new admin_setting_configdatetime($key, $label, $desc, $defaultset, array('tmask' => true, 'ymask' => true)));

    $actionoptions = array(
        '0' => get_string('keep', 'block_course_recycle'),
        '1' => get_string('reset', 'block_course_recycle'),
        '2' => get_string('throw', 'block_course_recycle')
    );

    $key = 'block_course_recycle/defaultaction';
    $label = get_string('configdefaultaction', 'block_course_recycle');
    $desc = get_string('configshowdate_desc', 'block_course_recycle');
    $settings->add(new admin_setting_configselect($key, $label, $desc, 1, $actionoptions));

    $numberoptions = array(
        '0' => get_string('nonotifications', 'block_course_recycle'),
        '1' => '1',
        '2' => '2',
        '3' => '3');

    $key = 'block_course_recycle/numberofnotifications';
    $label = get_string('confignumberofnotifications', 'block_course_recycle');
    $desc = get_string('confignumberofnotifications_desc', 'block_course_recycle');
    $settings->add(new admin_setting_configselect($key, $label, $desc, 3, $numberoptions));

    $key = 'block_course_recycle/notificationtext';
    $label = get_string('confignotificationtext', 'block_course_recycle');
    $desc = get_string('confignotificationtext_desc', 'block_course_recycle');
    $default = get_string('defaultnotification_tpl', 'block_course_recycle');
    $settings->add(new admin_setting_configtextarea($key, $label, $desc, $default));
}