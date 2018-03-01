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

class block_course_recycle_renderer extends plugin_renderer_base {

    public function globalstable(&$globals) {

        $throwstr = get_string('throwhdr', 'block_course_recycle');
        $archivestr = get_string('archivehdr', 'block_course_recycle');
        $keepstr = get_string('keephdr', 'block_course_recycle');
        $resetstr = get_string('resethdr', 'block_course_recycle');
        $unsetstr = get_string('unsethdr', 'block_course_recycle');

        $str = '';

        $str .= '<table class="generaltable"><tr>';
        $str .= '<th class="header">'.$throwstr.'</th>';
        $str .= '<th class="header">'.$archivestr.'</th>';
        $str .= '<th class="header">'.$keepstr.'</th>';
        $str .= '<th class="header">'.$resetstr.'</th>';
        $str .= '<th class="header">'.$unsetstr.'</th>';
        $str .= '</tr><tr>';
        $str .= '<td class="header">'.$globals['throw'].'</td>';
        $str .= '<td class="header">'.$globals['archive'].'</td>';
        $str .= '<td class="header">'.$globals['keep'].'</td>';
        $str .= '<td class="header">'.$globals['reset'].'</td>';
        $str .= '<td class="header">'.$globals['unset'].'</td>';
        $str .= '</tr></table>';

        return $str;
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
                $pix = $this->output->pix_url('reset'.$suffix, $title, 'block_course_recycle');
                $str .= '<center><a href="'.$ajax.'">'.$pix.'</a></center>';
        }

        return $str;
    }
}