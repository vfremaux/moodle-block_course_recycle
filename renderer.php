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
require_once($CFG->dirroot.'/blocks/course_recycle/classes/course_recycler.class.php');

use \block_course_recycle\course_recycler;

class block_course_recycle_renderer extends plugin_renderer_base {

    public function globalstable(&$globals) {

        $throwstr = get_string('throwhdr', 'block_course_recycle');
        $archivestr = get_string('archivehdr', 'block_course_recycle');
        $keepstr = get_string('keephdr', 'block_course_recycle');
        $resetstr = get_string('resethdr', 'block_course_recycle');
        $unsetstr = get_string('unsethdr', 'block_course_recycle');

        $template = new StdClass;

        $template->globals = (object) $globals;

        return $this->output->render_from_template('block_course_recycle/globals_table', $template);
    }

    public function recyclestates($recycleinstances) {
        return "Still in development";
    }

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

        $str = $this->output->render_from_template('block_course_recycle/modal_edit_form', $template);
        return $str;
    }

    /**
     * Displays a full table with course list and recycle status.
     * All courses in this list will at least being requesting recycle action or have action programmed for.
     */
    public function confirm_table($mycourses) {
        global $DB;

        $config = get_config('block_course_recycle');

        $template = new Stdclass;

        if (empty($mycourses)) {
            $template->emptylist = true;
            $template->nocoursenotification = $this->output->notification(get_string('nocourses', 'block_course_recycle'));
            return $this->output->render_from_template('block_course_recycle/confirm_table', $template);
        }

        foreach ($mycourses as $coursetpl) {

            $context = context_course::instance($coursetpl->id);
            $fields = 'u.id';
            $powerusers = get_users_by_capability($context, 'moodle/course:manageactivities', $fields);
            $coursetpl->editorcount = count($powerusers);
            $state = $DB->get_record('block_course_recycle', ['courseid' => $coursetpl->id]);
            $coursetpl->reason = get_string($state->reason, 'block_course_recycle');
            $coursetpl->fullname = $DB->get_field('course', 'fullname', ['id' => $coursetpl->id]);

            $coursetpl->status = $this->status_listform_part($coursetpl, $state, $context);

            $coursetpl->nextstate = '';
            $coursetpl->nextdate = '';
            $coursetpl->daystorun = '';
            if ($state->status == 'RequestForArchive' && $config->decisiondelay > 0) {
                if (!empty($config->defaultaction)) {
                    $coursetpl->nextstate = get_string($config->defaultaction, 'block_course_recycle');
                    $nextdate = $state->timemodified + $config->actiondelay * DAYSECS;
                    $daystorun = round((time() - $nextdate) / DAYSECS);
                    if ($daystorun < 0) {
                        $coursetpl->daystorun = $daystorun;
                    }
                    $coursetpl->nextdate = userdate($nextdate);
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
            $archivabletpl->actiondate = userdate($arch->timeprocessable);

            $template->archivables[] = $archivabletpl;
        }
        return $this->output->render_from_template('block_course_recycle/archivables_table', $template);
    }
}