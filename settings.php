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

    $key = 'block_course_recycle/blockstate';
    $label = get_string('configblockstate', 'block_course_recycle');
    $desc = get_string('configblockstate_desc', 'block_course_recycle');
    $states = array('active' => get_string('active', 'block_course_recycle'),
                    'remind1' => get_string('reminded1', 'block_course_recycle'),
                    'remind2' => get_string('reminded2', 'block_course_recycle'),
                    'remind3' => get_string('reminded3', 'block_course_recycle'),
                    'locked' => get_string('locked', 'block_course_recycle'),
                    'inactive' => get_string('inactive', 'block_course_recycle'));
    $settings->add(new admin_setting_configselect($key, $label, $desc, 'inactive', $states));

    $actionoptions = array(
        '0' => get_string('keep', 'block_course_recycle'),
        '1' => get_string('reset', 'block_course_recycle'),
        '2' => get_string('archive', 'block_course_recycle'),
        '3' => get_string('throw', 'block_course_recycle')
    );

    $key = 'block_course_recycle/defaultaction';
    $label = get_string('configdefaultaction', 'block_course_recycle');
    $desc = get_string('configdefaultaction_desc', 'block_course_recycle');
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

    $key = 'block_course_recycle/instancesperrun';
    $label = get_string('configinstancesperrun', 'block_course_recycle');
    $desc = get_string('configinstancesperrun_desc', 'block_course_recycle');
    $default = 20;
    $settings->add(new admin_setting_configtext($key, $label, $desc, $default));

    $key = 'block_course_recycle/archivestrategy';
    $label = get_string('configarchivestrategy', 'block_course_recycle');
    $desc = get_string('configarchivestrategy_desc', 'block_course_recycle');
    $archiveoptions = array('backup' => get_string('backup'));
    if (is_dir($CFG->dirroot.'/blocks/publishflow')) {
        $archiveoptions['publishflow'] = get_string('pluginname', 'block_publishflow');
    }
    $default = 'backup';
    $settings->add(new admin_setting_configselect($key, $label, $desc, $default, $archiveoptions));

    if (is_dir($CFG->dirroot.'/blocks/publishflow')) {
        include_once($CFG->dirroot.'/blocks/publishflow/xlib.php');
        $key = 'block_course_recycle/archivefactory';
        $label = get_string('configarchivefactory', 'block_course_recycle');
        $desc = get_string('configarchivefactory_desc', 'block_course_recycle');
        $archivefactoryoptions = block_publishflow_get_factories();
        if ($archivefactoryoptions) {
            $settings->add(new admin_setting_configselect($key, $label, $desc, '', $archivefactoryoptions));
        }
    }

    $key = 'block_course_recycle/archivesbackupdir';
    $label = get_string('configarchivesbackupdir', 'block_course_recycle');
    $desc = get_string('configarchivesbackupdir_desc', 'block_course_recycle');
    $default = '';
    $settings->add(new admin_setting_configtext($key, $label, $desc, $default));

    $key = 'block_course_recycle/recyclelogfile';
    $label = get_string('configlogfile', 'block_course_recycle');
    $desc = get_string('configlogfile_desc', 'block_course_recycle');
    $default = '%DATAROOT%/recycle.log';
    $settings->add(new admin_setting_configtext($key, $label, $desc, $default));
}