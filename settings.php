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
require_once($CFG->dirroot.'/blocks/course_recycle/compatlib.php');

use \block\course_recycle\admin_setting_configdatetime;

if ($ADMIN->fulltree) {

    $help = get_string('interactivesettingshelp', 'block_course_recycle');
    $settings->add(new admin_setting_heading('interactivesettings', get_string('interactivesettings', 'block_course_recycle'), $help));

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
        'Stay' => get_string('keep', 'block_course_recycle'),
        'Retire' => get_string('retire', 'block_course_recycle'),
        'Reset' => get_string('reset', 'block_course_recycle'),
        'Archive' => get_string('archive', 'block_course_recycle'),
        'Delete' => get_string('throw', 'block_course_recycle'),
    );

    $key = 'block_course_recycle/defaultaction';
    $label = get_string('configdefaultaction', 'block_course_recycle');
    $desc = get_string('configdefaultaction_desc', 'block_course_recycle');
    $settings->add(new admin_setting_configselect($key, $label, $desc, 'Stay', $actionoptions));

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

    $options = array(
        'standard' => get_string('standardmoodle', 'block_course_recycle'),
        'archive' => get_string('archivemoodle', 'block_course_recycle'),
    );
    $key = 'block_course_recycle/moodletype';
    $label = get_string('configmoodletype', 'block_course_recycle');
    $desc = get_string('configmoodletype_desc', 'block_course_recycle');
    $default = '';
    $settings->add(new admin_setting_configselect($key, $label, $desc, $default, $options));

    $settings->add(new admin_setting_heading('finishedsettings', get_string('finishedcoursessettings', 'block_course_recycle'), ''));

    $key = 'block_course_recycle/askowner';
    $label = get_string('configaskowner', 'block_course_recycle');
    $desc = get_string('configaskowner_desc', 'block_course_recycle');
    $default = 0;
    $settings->add(new admin_setting_configcheckbox($key, $label, $desc, $default));

    $actionoptions = array(
        RECYCLE_STAY => get_string('Stay', 'block_course_recycle'), // means "Do nothing at all"
        RECYCLE_RETIRE => get_string('Retire', 'block_course_recycle'), // means "keep and move to retired category"
        RECYCLE_RESET => get_string('Reset', 'block_course_recycle'), // means "keep then reset"
        RECYCLE_ARCHIVE => get_string('Archive', 'block_course_recycle'), // means "archive then delete"
        RECYCLE_DELETE => get_string('Delete', 'block_course_recycle'), // means "delete with no archiving"
        RECYCLE_CLONE => get_string('Clone', 'block_course_recycle'), // means "delete with no archiving"
        RECYCLE_CLONEANDRESET => get_string('CloneAndReset', 'block_course_recycle'), // means "delete with no archiving"
        RECYCLE_ARCHIVEANDDELETE => get_string('ArchiveAndDelete', 'block_course_recycle'), // means "delete with no archiving"
        RECYCLE_ARCHIVEANDRESET => get_string('ArchiveAndReset', 'block_course_recycle'), // means "delete with no archiving"
        RECYCLE_ARCHIVECLONEANDRESET => get_string('ArchiveCloneAndReset', 'block_course_recycle'), // means "delete with no archiving"
    );

    $key = 'block_course_recycle/defaultactionfinishedcourses';
    $label = get_string('configdefaultactionfinishedcourses', 'block_course_recycle');
    $desc = get_string('configdefaultactionfinishedcourses_desc', 'block_course_recycle');
    $settings->add(new admin_setting_configselect($key, $label, $desc, 'Stay', $actionoptions));

    $actionoptions = array(
        RECYCLE_STAY => get_string('keep', 'block_course_recycle'), // means "Do nothing at all"
        RECYCLE_RETIRE => get_string('retire', 'block_course_recycle'), // means "keep and move to retired category"
        RECYCLE_RESET => get_string('reset', 'block_course_recycle'), // means "keep then reset"
        RECYCLE_ARCHIVE => get_string('archive', 'block_course_recycle'), // means "archive then delete"
        RECYCLE_DELETE => get_string('throw', 'block_course_recycle'), // means "delete with no archiving"
    );

    $key = 'block_course_recycle/defaultiafcourses';
    $label = get_string('configdefaultiafcourses', 'block_course_recycle');
    $desc = get_string('configdefaultiafcourses_desc', 'block_course_recycle');
    $settings->add(new admin_setting_configselect($key, $label, $desc, 'Stay', $actionoptions));

    // If 0, no decision delay. the course remains.
    $key = 'block_course_recycle/decisiondelay';
    $label = get_string('configdecisiondelay', 'block_course_recycle');
    $desc = get_string('configdecisiondelay_desc', 'block_course_recycle');
    $default = 14;
    $settings->add(new admin_setting_configtext($key, $label, $desc, $default));

    // If 0, no decision delay. the course remains.
    $key = 'block_course_recycle/actiondelay';
    $label = get_string('configactiondelay', 'block_course_recycle');
    $desc = get_string('configactiondelay_desc', 'block_course_recycle');
    $default = 14;
    $settings->add(new admin_setting_configtext($key, $label, $desc, $default));

    $catlist = block_course_recycle\compat::get_catlist();

    $key = 'block_course_recycle/retirecategory';
    $label = get_string('configretirecategory', 'block_course_recycle');
    $desc = get_string('configretirecategory_desc', 'block_course_recycle');
    $default = '';
    $settings->add(new admin_setting_configselect($key, $label, $desc, '', $catlist));

    $key = 'block_course_recycle/policyenddate';
    $label = get_string('configpolicyenddate', 'block_course_recycle');
    $desc = get_string('configpolicyenddate_desc', 'block_course_recycle');
    $default = 1;
    $settings->add(new admin_setting_configcheckbox($key, $label, $desc, $default));

    $key = 'block_course_recycle/policyenrols';
    $label = get_string('configpolicyenrols', 'block_course_recycle');
    $desc = get_string('configpolicyenrols_desc', 'block_course_recycle');
    $default = 0;
    $settings->add(new admin_setting_configcheckbox($key, $label, $desc, $default));

    $key = 'block_course_recycle/policylastaccess';
    $label = get_string('configpolicylastaccess', 'block_course_recycle');
    $desc = get_string('configpolicylastaccess_desc', 'block_course_recycle');
    $default = 0;
    $settings->add(new admin_setting_configcheckbox($key, $label, $desc, $default));

    $key = 'block_course_recycle/mininactivedaystofinish';
    $label = get_string('configmininactivedaystofinish', 'block_course_recycle');
    $desc = get_string('configmininactivedaystofinish_desc', 'block_course_recycle');
    $default = 30;
    $settings->add(new admin_setting_configtext($key, $label, $desc, $default));

    $key = 'block_course_recycle/minactiveaccesstomaintain';
    $label = get_string('configminactiveaccesstomaintain', 'block_course_recycle');
    $desc = get_string('configminactiveaccesstomaintain_desc', 'block_course_recycle');
    $default = 3;
    $settings->add(new admin_setting_configtext($key, $label, $desc, $default));

    $key = 'block_course_recycle/minhitstomaintain';
    $label = get_string('configminhitstomaintain', 'block_course_recycle');
    $desc = get_string('configminhitstomaintain_desc', 'block_course_recycle');
    $default = 50;
    $settings->add(new admin_setting_configtext($key, $label, $desc, $default));

    $options = [
        0 => get_string('rfannone', 'block_course_recycle'),
        1 => get_string('rfanoldestet', 'block_course_recycle'),
        2 => get_string('rfanallets', 'block_course_recycle')
    ];
    $key = 'block_course_recycle/requestforarchivenotification';
    $label = get_string('configrequestforarchivenotification', 'block_course_recycle');
    $desc = get_string('configrequestforarchivenotification_desc', 'block_course_recycle');
    $default = 0;
    $settings->add(new admin_setting_configselect($key, $label, $desc, $default, $options));

    $settings->add(new admin_setting_heading('archiversettings', get_string('archiversettings', 'block_course_recycle'), ''));

    $key = 'block_course_recycle/sourcewwwroot';
    $label = get_string('configsourcewwwroot', 'block_course_recycle');
    $desc = get_string('configsourcewwwroot_desc', 'block_course_recycle');
    $default = '';
    $settings->add(new admin_setting_configtext($key, $label, $desc, $default, PARAM_TEXT));

    $key = 'block_course_recycle/sourcetoken';
    $label = get_string('configsourcetoken', 'block_course_recycle');
    $desc = get_string('configsourcetoken_desc', 'block_course_recycle');
    $default = '';
    $settings->add(new admin_setting_configtext($key, $label, $desc, $default, PARAM_TEXT));

    $key = 'block_course_recycle/preservesourcecategory';
    $label = get_string('configpreservesourcecategory', 'block_course_recycle');
    $desc = get_string('configpreservesourcecategory_desc', 'block_course_recycle');
    $default = 1;
    $settings->add(new admin_setting_configcheckbox($key, $label, $desc, $default));
}