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
 * Main block file. let the teacher declare what they want to do with the course
 * at the end of the year.
 *
 * @package   block_course_recycle
 * @category  blocks
 * @copyright 2014 Valery Fremaux (valery.fremaux@gmail.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/blocks/course_recycle/lib.php');
require_once($CFG->dirroot.'/blocks/course_recycle/locallib.php');

class block_course_recycle extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_course_recycle');
    }

    public function has_config() {
        return true;
    }

    public function applicable_formats() {
        return array('all' => true, 'course' => true, 'site' => true);
    }

    public function specialization() {
    }

    public function instance_allow_multiple() {
        return false;
    }

    public function hide_header() {
        $config = get_config('block_course_recycle');

        $systemcontext = context_system::instance();

        if ($config->blockstate == 'inactive' && !has_capability('moodle/site:config', $systemcontext)) {
            return true;
        }
    }

    public function get_content() {
        global $PAGE, $OUTPUT, $COURSE, $DB;

        $renderer = $PAGE->get_renderer('block_course_recycle');
        $config = get_config('block_course_recycle');

        if (!isset($config->blockstate)) {
            $config->blockstate = 'active';
        }

        $this->content = new StdClass;
        $this->content->text = '';
        $this->content->footer = '';

        $blockcontext = context_block::instance($this->instance->id);
        if (!has_capability('block/course_recycle:view', $blockcontext)) {
            return $this->content;
        }

        $state = new StdClass;
        $state->status = 'undefined';

        if ($COURSE->id > SITEID) {

            $oldstate = $DB->get_field('block_course_recycle', 'status', ['courseid' => $COURSE->id]);
            if ($oldstate) {
                $state->status = $oldstate;

                $recycleaction = optional_param('recycleaction', false, PARAM_TEXT);
                if ($recycleaction) {
                    $this->config = new StdClass;
                    if ($state) {
                        $state->status = $recycleaction;
                        $state->timemodified = time();
                        $state->lastuserid = $USER->id;
                        $DB->update_record('block_course_recycle', $state);
                    } else {
                        $state->courseid = $COURSE->id;
                        $state->status = $recycleaction;
                        $state->timemodified = time();
                        $state->lastuserid = $USER->id;
                        $state->id = $DB->insert_record('block_course_recycle', $state);
                    }
                }
            }

            // Not in period.
            if ($config->blockstate == 'inactive') {
                $this->content = new StdClass;
                $this->content->text = '';
                $this->content->footer = '';
                return $this->content;
            }

            if (empty($this->config)) {
                $this->config = new StdClass;
            }

            $this->content->text = '';
            $this->content->text .= $OUTPUT->box_start('', 'block-recycle-state');
            $this->content->text .= $renderer->recyclebutton($state->status, $this->instance->id);
            $this->content->text .= $OUTPUT->box_end();

            $this->content->footer = '';

            $task = \core\task\manager::get_scheduled_task('\\block_course_recycle\\task\\lock_task');
            if (!$task->get_disabled()) {
                if ($config->blockstate != 'locked') {
                    $taskdate = $task->get_next_run_time();
                    $this->content->footer .= get_string('opentill', 'block_course_recycle', userdate($taskdate));
                } else {
                    $this->content->footer .= get_string('choicelocked', 'block_course_recycle');
                }
            }
        }

        if ($config->moodletype == 'standard') {
            if (block_course_recycle_has_capability_somewhere('moodle/course:manageactivities', false, true, true, CONTEXT_COURSE)) {
                $this->content->footer .= '<br/>';
                $stateurl = new moodle_url('/blocks/course_recycle/confirmmycourses.php');
                $this->content->footer .= '<a href="'.$stateurl.'">'.get_string('stateyourcourses', 'block_course_recycle').'</a>';
            }
        }
        // Archive node.
        $contextsystem = context_system::instance();
        if (has_capability('moodle/site:config', $contextsystem)) {
            $this->content->footer .= '<br/>';
            $indexurl = new moodle_url('/blocks/course_recycle/index.php', array('courseid' => $COURSE->id));
            $this->content->footer .= '<a href="'.$indexurl.'">'.get_string('recycle', 'block_course_recycle').'</a>';
        }

        return $this->content;
    }

    /**
     * Serialize and store config data
     */
    public function instance_config_save($data, $nolongerused = false) {

        $config = clone($data);
        parent::instance_config_save($config, $nolongerused);
    }

    public function instance_delete() {

        $fs = get_file_storage();
        $fs->delete_area_files($this->context->id, 'block_course_recycle');
        return true;
    }

    /**
     * The block should only be dockable when the title of the block is not empty
     * and when parent allows docking.
     *
     * @return bool
     */
    public function instance_can_be_docked() {
        return (!empty($this->config->title) && parent::instance_can_be_docked());
    }

    public function get_required_javascript() {
        global $CFG, $PAGE;

        parent::get_required_javascript();
        $PAGE->requires->js('/blocks/course_recycle/js/recycle.js');
    }

    /*
     * Obsolete using programmed task for global scheduling
     *
    public static function compare_date($d1, $d2) {

        $date1 = get_date($d1);
        $date2 = get_date($d2);

        if ($date1['mon'] > $date2['mon']) {
            return true;
        }

        if ($date1['mon'] < $date2['mon']) {
            return false;
        }

        if ($date1['mday'] > $date2['mday']) {
            return true;
        }

        if ($date1['mday'] < $date2['mday']) {
            return false;
        }

        if ($date1['hours'] > $date2['hours']) {
            return true;
        }

        if ($date1['hours'] < $date2['hours']) {
            return false;
        }
    }
    */

    public function crontask() {
        global $DB;

        $config = get_config('block_course_recycle');

        $f = null;
        if (!empty($config->logfile)) {
            $config->logfile = str_replace('%DATAROOT%', $CFG->dataroot, $config->logfile);
            $f = fopen($logfile, 'w');
        }

        $recycles = $DB->get_records('block_instances', array('blockname' => 'course_recycle'));

        if (!empty($recycles)) {
            foreach ($recycle as $rc) {
                $blockconfig = base64_decode($rc->configdata);

                if ($blockconfig->choicedone) {
                    switch ($blockconfig) {
                        case 'throw': {
                            // Get the course :
                            $context = context::get_from_id($blockconfig->parentcontextid);
                            $course = $DB->get_record('course', array('id' => $context->instanceid));
                            // Delete.
                            if ($f) {
                                fputs($f, 'RECYCLE DELETE course '.$context->instanceid."\n");
                            }
                            course_delete_course($course, false); // Do not show feedback.
                            if ($f) {
                                fputs($f, "Deleted.\n");
                            }
                            break;
                        }

                        case 'reset': {
                            $data = new StdClass;
                            // ... TODO : Fill all resetdata subkeys. (As many as possible)
                            $data->courseid = $course->id;
                            if ($f) {
                                fputs($f, 'RECYCLE RESETTING course '.$context->instanceid."\n");
                            }
                            reset_course_userdata($data);
                            if ($f) {
                                fputs($f, "Reset.\n");
                            }
                            break;
                        }

                        case 'keep': {
                            if ($f) {
                                fputs($f, 'RECYCLE KEEPING course '.$context->instanceid."\n");
                                fputs($f, "No op.\n");
                            }
                            break;
                        }

                        case 'archive': {
                            // Activate the archive active plugin strategy.
                            if ($config->archivestrategy == 'backup') {
                                // Standard backup automation.
                            } else if ($config->archivestrategy == 'publishflow') {
                                // Pushes the course to a publishflow equiped platform.
                                include_once($CFG->dirroot.'/blocks/publishflow/xlib.php');
                                if ($f) {
                                    fputs($f, 'RECYCLE ARCHIVING course '.$context->instanceid.' with '.$config->archivestrategy."\n");
                                }
                                $whereid = $config->archivefactory;
                                $where = $DB->get_record('mnet_host', array('id' => $config->archivefactory));
                                if ($where) {
                                    block_publishflow_retrofit_course($context->instanceid, $where->wwwroot);
                                    if ($f) {
                                        fputs($f, "Backup.\n");
                                    }
                                } else {
                                    if ($f) {
                                        fputs($f, "Failed, empty or null archiving moodle.\n");
                                    }
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }

        if ($f) {
            fclose($f);
        }

    }
}
