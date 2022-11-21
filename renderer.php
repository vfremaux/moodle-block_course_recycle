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

require_once($CFG->dirroot.'/blocks/course_recycle/locallib.php');
require_once($CFG->dirroot.'/blocks/course_recycle/compatlib.php');
require_once($CFG->dirroot.'/blocks/course_recycle/classes/course_recycler.class.php');

use \block_course_recycle\course_recycler;
use \block_course_recycle\compat;

class block_course_recycle_renderer extends plugin_renderer_base {

    public function globalstable(&$globals) {

        $template = new StdClass;

        $template->globals = (object) $globals;

        return $this->output->render_from_template('block_course_recycle/globals_table', $template);
    }

    public function recyclestates($recycleinstances) {
        return "Still in development";
    }

    /**
     * Use in scheduled running mode.
     * @param string $recycleaction
     * @param integer $blockid
     */
    public function recyclebutton($recycleaction, $blockid) {
        global $COURSE, $USER;

        $str = '';

        $config = get_config('block_course_recycle');

        $suffix = '';
        if (@$config->blockstate == 'locked') {
            $suffix = '_locked';
        }

        switch ($recycleaction) {
            case 'throw': {
                $ajax = 'javascript:ajax_recycle_change_action('.$COURSE->id.', '.$blockid.', '.$USER->id.', \'keep\');';
                $title = get_string('throw', 'block_course_recycle');
                $pix = $this->output->pix_icon('throw'.$suffix, $title, 'block_course_recycle');
                $str .= '<center><a href="'.$ajax.'" title="'.$title.'">'.$pix.'</a></center>';
                break;
            }

            case 'keep': {
                $ajax = 'javascript:ajax_recycle_change_action('.$COURSE->id.', '.$blockid.', '.$USER->id.', \'reset\');';
                $title = get_string('keep', 'block_course_recycle');
                $pix = $this->output->pix_icon('keep'.$suffix, $title, 'block_course_recycle');
                $str .= '<center><a href="'.$ajax.'">'.$pix.'</a></center>';
                break;
            }

            case 'archive': {
                $ajax = 'javascript:ajax_recycle_change_action('.$COURSE->id.', '.$blockid.', '.$USER->id.', \'archive\');';
                $title = get_string('archive', 'block_course_recycle');
                $pix = $this->output->pix_icon('archive'.$suffix, $title, 'block_course_recycle');
                $str .= '<center><a href="'.$ajax.'">'.$pix.'</a></center>';
                break;
            }

            case 'reset':
            default :
                $ajax = 'javascript:ajax_recycle_change_action('.$COURSE->id.', '.$blockid.', '.$USER->id.', \'throw\');';
                $title = get_string('reset', 'block_course_recycle');
                $pix = $this->output->pix_icon('reset'.$suffix, $title, 'block_course_recycle');
                $str .= '<center><a href="'.$ajax.'">'.$pix.'</a></center>';
        }

        return $str;
    }

    public function recycletaskbutton($courseid) {

        $buttonurl = new moodle_url('/blocks/course_recycle/index.php', ['confirm' => 1, 'courseid' => $courseid]);
        echo $this->output->single_button($buttonurl, get_string('dorecycle', 'block_course_recycle'));
    }

    public function status_listform_part($course, $state, $context) {

        $fullkeys = course_recycler::get_status_list();

        $template = new StdClass;

        $template->code = $state->status;
        $template->static = $fullkeys[$state->status];

        $systemcontext = context_system::instance();

        $template->editlink = false;
        if (has_capability('moodle/site:config', $systemcontext)) {
            $template->editlink = true;
        } else if (has_capability('moodle/course:manageactivities', $context)) {
            $template->editlink = true;
        }
        return $template;
    }

    /**
     * Renders form for modal edition
     */
    public function modal_form($courseid) {
        global $DB;

        $config = get_config('block_course_recycle');

        $systemcontext = context_system::instance();

        $state = $DB->get_field('block_course_recycle', 'status', ['id' => $courseid]);
        $template = new Stdclass;

        $template->canretire = false;
        if (!empty($config->retirecategory)) {
            $template->canretire = true;
        }

        $template->isadmin = has_capability('moodle/site:config', $systemcontext);

        $str = $this->output->render_from_template('block_course_recycle/modal_edit_form', $template);
        return $str;
    }

    /**
     * Displays a full table with course list and recycle status.
     * All courses in this list will at least being requesting recycle action or have action programmed for.
     */
    public function confirm_table($mycourses, $topcatid) {
        global $DB;

        $config = get_config('block_course_recycle');
        $statuslist = course_recycler::get_status_list();

        $template = new Stdclass;

        $template->confirmurl = new moodle_url('/blocks/course_recycle/confirmmycourses.php');
        $template->sesskey = sesskey();
        $template->topcatid = $topcatid;

        $template->categoryfilter = $this->category_filter($id, $topcatid);
        $context = context_system::instance();
        $template->isadmin = has_capability('moodle/site:config', $context);
        if ($template->isadmin) {

            // Task status monitor.
            $tasks = $DB->get_records('task_scheduled', ['component' => 'block_course_recycle']);
            foreach ($tasks as $task) {
                if (preg_match('/discover/', $task->classname)) {
                    $template->discovertaskstate = !$task->disabled;
                }
                if (preg_match('/recycle_courses/', $task->classname)) {
                    $template->recycletaskstate = !$task->disabled;
                }
            }
        }

        if (empty($mycourses)) {
            $template->emptylist = true;
            $template->nocoursenotification = $this->output->notification(get_string('nocourses', 'block_course_recycle'));
            return $this->output->render_from_template('block_course_recycle/confirm_table', $template);
        }

        if (5 < count($mycourses)) {
            $template->hasmany = true;
            $template->changeselect = html_writer::select($statuslist, 'changestatus', '', ['' => 'choose']);
        }

        foreach ($mycourses as $coursetpl) {

            $context = context_course::instance($coursetpl->id);
            $fields = 'u.id';
            $powerusers = get_users_by_capability($context, 'moodle/course:manageactivities', $fields);
            $coursetpl->editorcount = count($powerusers);
            $state = $DB->get_record('block_course_recycle', ['courseid' => $coursetpl->id]);

            if ($coursetpl->editorcount == 0 && $state->status == RECYCLE_ASK) {
                $coursetpl->nopeerwarning = true;
            }

            $coursetpl->reason = get_string($state->reason, 'block_course_recycle');
            $coursetpl->fullname = $DB->get_field('course', 'fullname', ['id' => $coursetpl->id]);
            $coursetpl->courseurl = new moodle_url('/course/view.php', ['id' => $coursetpl->id]);

            $coursetpl->status = $this->status_listform_part($coursetpl, $state, $context);

            $coursetpl->nextstate = '';
            $coursetpl->nextdate = '';
            $coursetpl->daystorun = '';

            // If we have a default action set... and no status or requesting expired status, take default action.

            $coursetpl->nextstate = course_recycler::get_current_action($state->status);
            $coursetpl->nextstatestr = $statuslist[$coursetpl->nextstate];

            if ($state->status == RECYCLE_ASK) {

                $coursetpl->nextstate = $config->defaultactionfinishedcourses;
                $coursetpl->nextstatestr = $statuslist[$coursetpl->nextstate];

                // If ask to owner is the current status.
                if ($config->decisiondelay > 0 || $config->actiondelay > 0) {
                    $nextdate = $state->timemodified + ($config->decisiondelay + $config->actiondelay) * DAYSECS;
                    $daystorun = round((time() - $nextdate) / DAYSECS);
                    if ($daystorun < 0) {
                        $coursetpl->daystorun = $daystorun;
                    }
                    $coursetpl->nextdate = userdate($nextdate);
                } else {
                    $coursetpl->nextdate = userdate(time());
                    $coursetpl->daystorun = 0;
                }
            } else {
                // For all other status that have a next action.
                if (!in_array($state->status, [RECYCLE_STAY, RECYCLE_DONE, RECYCLE_FAILED, RECYCLE_ASK])) {
                    if ($config->actiondelay > 0) {
                        $nextdate = $state->timemodified + $config->actiondelay * DAYSECS;
                        $daystorun = round((time() - $nextdate) / DAYSECS);
                        if ($daystorun < 0) {
                            $coursetpl->daystorun = $daystorun;
                        }
                        $coursetpl->nextdate = userdate($nextdate);
                    } else {
                        $coursetpl->nextdate = userdate(time());
                        $coursetpl->daystorun = 0;
                    }
                }
            }

            $label = get_string('notificationstopped', 'block_course_recycle');
            $coursetpl->notify = ($state->stopnotify) ? $OUTPUT->pix_icon('stopnotify', $label, 'block_course_recycle') : '' ;

            $template->courses[] = $coursetpl;
        }

        return $this->output->render_from_template('block_course_recycle/confirm_table', $template);
    }

    public function list_archivables($archivables) {
        $template = new StdClass;


        if (empty($archivables)) {
            $template->emptylist = true;
            $template->nocoursetoarchivenotification = $this->output->notification(get_string('nocoursestoarchive', 'block_course_recycle'));
            return $this->output->render_from_template('block_course_recycle/archivables_table', $template);
        }

        $template->emptylist = false;
        foreach ($archivables as $arch) {
            $archivabletpl = $arch;
            if (empty($arch->timeprocessable)) {
                $arch->timeprocessable = time();
            }
            $archivabletpl->actiondate = userdate($arch->timeprocessable);

            $template->archivables[] = $archivabletpl;
        }
        return $this->output->render_from_template('block_course_recycle/archivables_table', $template);
    }

    public function category_filter($id, $topcatid) {

        $cats = compat::get_catlist();
        $template = new Stdclass();
        $template->id = $id;
        $template->select = html_writer::select($cats, 'topcatid', $topcatid, ['' => '...']);

        return $this->output->render_from_template('block_course_recycle/catfilter', $template);
    }
}