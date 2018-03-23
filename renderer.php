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
 * @copyright   2013 onwards Valery Fremaux (http://www.mylearningfactory.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

class block_course_recycle_renderer extends plugin_renderer_base {

    function globalstable(&$globals) {

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

    function recyclebutton($theblock, $url) {
        global $OUTPUT, $COURSE, $USER, $CFG;

        $str = '<div id="block-recycle-state">';

        switch ($theblock->config->recycleaction) {
            case 'throw':
                $ajax = 'javascript:ajax_recycle_change_action('.$COURSE->id.', '.$USER->id.', \'keep\');';
                $pixurl = $OUTPUT->pix_url('throw', 'block_course_recycle');
                $title = get_string('throw', 'block_course_recycle');
                $str .= '<center><a href="'.$ajax.'"><img width="30%" src="'.$pixurl.'" title="'.$title.'"/></a></centrer>';
                break;

            case 'keep':
                $ajax = 'javascript:ajax_recycle_change_action('.$COURSE->id.', '.$USER->id.', \'reset\');';
                $pixurl = $OUTPUT->pix_url('keep', 'block_course_recycle');
                $title = get_string('keep', 'block_course_recycle');
                $str .= '<center><a href="'.$ajax.'"><img width="30%" src="'.$pixurl.'" title="'.$title.'"/></a></centrer>';
                break;

            case 'reset':
            default :
                $ajax = 'javascript:ajax_recycle_change_action('.$COURSE->id.', '.$USER->id.', \'throw\');';
                $pixurl = $OUTPUT->pix_url('reset', 'block_course_recycle');
                $title = get_string('reset', 'block_course_recycle');
                $str .= '<center><a href="'.$ajax.'"><img width="30%" src="'.$pixurl.'" title="'.$title.'"/></a></centrer>';
        }

        return $str;
    }
}