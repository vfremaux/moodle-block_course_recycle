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

namespace block_course_recycle;

use StdClass;

defined('MOODLE_INTERNAL') || die();

class confirm_controller {

    public function receive($cmd, $data = array()) {

        if (!empty($data)) {
            // Data is fed from outside.
            $this->data = (object)$data;
            $this->received = true;
            return;
        } else {
            $this->data = new StdClass;
        }

        switch ($cmd) {
            case "groupchange": {
                $ksels = preg_grep('/^sel/', array_keys($_POST));
                foreach ($ksels as $ks) {
                    // collect and clean ids.
                    $s = clean_param($ks, PARAM_TEXT);
                    $s = str_replace('sel', '', $s);
                    $this->data->ids[] = $s;
                }
                $this->received = true;
            }
        }
    }

    public function process($cmd) {
        global $USER, $DB;

        if (!$this->received) {
            throw new \coding_exception('Data must be received in controller before operation. this is a programming error.');
        }

        switch ($cmd) {
            case "groupchange": {
                $changeto = required_param('changestatus', PARAM_TEXT);

                foreach ($this->data->ids as $cid) {

                    $state = $DB->get_record('block_course_recycle', ['courseid' => $cid]);
                    if (!$state) {
                        $state = new Stdclass;
                        $state->courseid = $cid;
                        $state->status = $changeto;
                        $state->postactions = course_recycler::get_post_action($state->status);
                        $state->timemodified = time();
                        $state->lastuserid = $USER->id;
                        $DB->insert_record('block_course_recycle', $state);
                    } else {
                        $state->status = $changeto;
                        $state->timemodified = time();
                        $state->postactions = course_recycler::get_post_action($state->status);
                        $state->lastuserid = $USER->id;
                        $DB->update_record('block_course_recycle', $state);
                    }
                }
            }
        }
    }
}
