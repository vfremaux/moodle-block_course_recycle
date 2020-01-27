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
 * Event observers used for course recycling.
 *
 * @package    block_course_recycle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (!function_exists('debug_trace')) {
    function debug_trace($message, $label = '') {
        assert(1);
    }
}

/**
 * Event observer for block course recycle.
 */
class block_course_recycle_observer {

    /**
     * This will purge the recycling register from this course entry
     * @param object $event
     */
    public static function on_course_deleted(\core\event\course_deleted $event) {
        global $DB;

        if (function_exists('debug_trace')) {
            debug_trace("Course recycle observer : clean recycle register");
        }

        $DB->delete_records('block_course_recycle', ['courseid' => $event->courseid]);
    }
}
