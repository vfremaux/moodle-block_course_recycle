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
namespace block_course_recycle;

defined('MOODLE_INTERNAL') || die;

// Compatibility functions.

class compat {

    public static function create_category($catdata) {
        \core_course_category::create($catdata);
    }

    public static function get_catlist($capability = '') {
        if (empty($capability)) {
            $capability = 'moodle/course:create';
        }
        $mycatlist = \core_course_category::make_categories_list('moodle/course:create');
        return $mycatlist;
    }

    public static function get_category($catid) {
        return \core_course_category::get($catid);
    }

    public static function get_course_list($course) {
    	return new \core_course_list_element($course);
    }

    public static function get_default_coursecat() {
        return \core_course_category::get_default();
    }

    public static function has_capability_on_any_coursecat($capabilities) {
        return \core_course_category::has_capability_on_any($capabilities);
    }

    public static function get_many_categories($categories) {
        return \core_course_category::get_many($categories);
    }

    public static function resort_categories_cleanup($sortcoursesby) {
        return \core_course_category::resort_categories_cleanup($sortcoursesby !== false);
    }
}