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

        $throwstr = get_string('throw', 'block_course_recycle');
        $keepstr = get_string('keep', 'block_course_recycle');
        $resetstr = get_string('reset', 'block_course_recycle');
        $unsetstr = get_string('unset', 'block_course_recycle');

        $str .= '';

        $str .= '<table><tr>';
        $str .= '<th class="header">'.$throwstr.'</th>';
        $str .= '<th class="header">'.$keepstr.'</th>';
        $str .= '<th class="header">'.$resetstr.'</th>';
        $str .= '<th class="header">'.$unsetstr.'</th>';
        $str .= '</tr><tr>';
        $str .= '<td class="header">'.$globals['throw'].'</td>';
        $str .= '<td class="header">'.$globals['keep'].'</td>';
        $str .= '<td class="header">'.$globals['reset'].'</td>';
        $str .= '<td class="header">'.$globals['unset'].'</td>';
        $str .= '</tr></table>';
    }

    public function recyclebutton($theblock, $url) {
        global $COURSE, $USER;

        $str = '<div id="block-recycle-state">';

        switch ($theblock->config->recycleaction) {
            case 'throw': {
                $ajax = 'javascript:ajax_recycle_change_action('.$COURSE->id.', '.$USER->id.', \'keep\');';
                $pixurl = $this->output->pix_url('throw', 'block_course_recycle');
                $pix = '<img width="30%" src="'.$pixurl.'" title="'.get_string('throw', 'block_course_recycle').'"/>';
                $str .= '<center><a href="'.$ajax.'">'.$pix.'</a></centrer>';
                break;
            }

            case 'keep': {
                $ajax = 'javascript:ajax_recycle_change_action('.$COURSE->id.', '.$USER->id.', \'reset\');';
                $pixurl = $this->output->pix_url('keep', 'block_course_recycle');
                $pix = '<img width="30%" src="'.$pixurl.'" title="'.get_string('keep', 'block_course_recycle').'"/>';
                $str .= '<center><a href="'.$ajax.'">'.$pix.'</a></centrer>';
                break;
            }

            case 'reset':
            default :
                $ajax = 'javascript:ajax_recycle_change_action('.$COURSE->id.', '.$USER->id.', \'throw\');';
                $pixurl = $this->output->pix_url('reset', 'block_course_recycle');
                $pix = '<img width="30%" src="'.$pixurl.'" title="'.get_string('reset', 'block_course_recycle').'"/>';
                $str .= '<center><a href="'.$ajax.'">'.$pix.'</a></centrer>';
        }

        return $str;
    }
}