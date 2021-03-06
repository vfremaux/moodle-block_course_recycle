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
 * @package    block_course_recycle
 * @category   blocks
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @copyright  Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block\course_recycle;

defined('MOODLE_INTERNAL') || die;

/**
 * Time selector
 *
 * This is a liiitle bit messy. we're using two selects, but we're returning
 * them as an array named after $name (so we only use $name2 internally for the setting)
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configdatetime extends \admin_setting {

    public $options;

    /**
     * Constructor
     * @param string $hoursname setting for hours
     * @param string $minutesname setting for hours
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting array representing default time 'h'=>hours, 'm'=>minutes, 'y'=>year, 'M'=>month, 'd'=>Day
     */
    public function __construct($datename, $visiblename, $description, $defaultsetting, $options = null) {
        $this->options = $options;
        parent::__construct($datename, $visiblename, $description, $defaultsetting);
    }

    /**
     * Get the selected time
     *
     * @return mixed An array containing 'h'=>xx, 'm'=>xx, 'y'=>xxxx, 'M'=>xx, 'd'=>xx, or null if not set
     */
    public function get_setting() {
        $result = $this->config_read($this->name);

        $datearr = getdate((float) $result);

        $data = array('h' => $datearr['hours'],
            'm' => $datearr['minutes'],
            'y' => $datearr['year'],
            'M' => $datearr['mon'],
            'd' => $datearr['mday']);
        return $data;
    }

    /**
     * Store the time as unix timestamp
     *
     * @param array $data Must be form 'y' => xxxx, 'M' => xx, 'd' => xx, 'h'=>xx, 'm'=>xx
     * @return bool true if success, false if not
     */
    public function write_setting($data) {
        if (!is_array($data)) {
            return '';
        }

        $datetime = mktime((float) $data['h'], (float) $data['m'], 0, (float) $data['M'], (float) $data['d'], (float) $data['y']);

        $result = $this->config_write($this->name, $datetime);
        return ($result ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Returns XHTML time select fields
     *
     * @param array $data Must be form 'h'=>xx, 'm'=>xx, 'y'=>xxxx, 'M'=>xx, 'd'=>xx
     * @param string $query
     * @return string XHTML time select fields and wrapping div(s)
     */
    public function output_html($data, $query = '') {
        $default = $this->get_defaultsetting();

        $defaultinfo = '';

        if (is_array($default)) {
            if (empty($this->options['ymask'])) {
                $defaultinfo .= $default['y'].'-';
            }
            $defaultinfo .= $default['M'].'-'.$default['d'].' ';
            if (empty($this->options['tmask'])) {
                $defaultinfo .= $default['h'].':'.$default['m'];
            }
        } else {
            $defaultinfo = null;
        }
        if ($data['y'] == 1970) {
            $data = $default;
        }

        $return = '<div class="form-datetime defaultsnext">';

        if (empty($this->options['ymask'])) {
            $return .= '<select id="'.$this->get_id().'y" name="'.$this->get_full_name().'[y]">';
            for ($i = 2010; $i < 2030; $i++) {
                $return .= '<option value="'.$i.'"'.($i == $data['y'] ? ' selected="selected"' : '').'>'.$i.'</option>';
            }
        } else {
            // We do not care really of the year, but need one to record the date in settings.
            $return .= '<input type="hidden" name="'.$this->get_full_name().'[y]" value="'.date('Y', time()).'" />';
        }
        $return .= '</select>';

        $return .= '<select id="'.$this->get_id().'M" name="'.$this->get_full_name().'[M]">';
        for ($i = 1; $i <= 12; $i++) {
            $return .= '<option value="'.$i.'"'.($i == $data['M'] ? ' selected="selected"' : '').'>'.sprintf('%02d', $i).'</option>';
        }
        $return .= '</select>';

        $return .= ' <select id="'.$this->get_id().'d" name="'.$this->get_full_name().'[d]">';
        for ($i = 1; $i <= 31; $i++) {
            $return .= '<option value="'.$i.'"'.($i == $data['d'] ? ' selected="selected"' : '').'>'.sprintf('%02d', $i).'</option>';
        }
        $return .= '</select>';

        if (empty($this->options['tmask'])) {
            $return .= '<select id="'.$this->get_id().'h" name="'.$this->get_full_name().'[h]">';
            for ($i = 0; $i < 24; $i++) {
                $return .= '<option value="'.$i.'"'.($i == $data['h'] ? ' selected="selected"' : '').'>'.$i.'</option>';
            }
            $return .= '</select>';

            $return .= ':<select id="'.$this->get_id().'m" name="'.$this->get_full_name().'[m]">';
            for ($i = 0; $i < 60; $i += 5) {
                $return .= '<option value="'.$i.'"'.($i == $data['m'] ? ' selected="selected"' : '').'>'.$i.'</option>';
            }
            $return .= '</select>';
        } else {
            // Defaults to midnight
            $return .= '<input type="hidden" name="'.$this->get_full_name().'[h]" value="O" />';
            $return .= '<input type="hidden" name="'.$this->get_full_name().'[m]" value="0" />';
        }

        $return .= '</div>';
        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', $defaultinfo, $query);
    }
}
