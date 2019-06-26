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
 * @package block_course_recycle
 * @category blocks
 * @author Valery Fremaux (valery.fremaux@gmail.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL
 */
defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");
require_once($CFG->dirroot."/blocks/course_recycle/classes/course_recycler.class.php");

use block_course_recycle\course_recycler;

/**
 * Software environement wrappers
 *
 * This set of functions define wrappers to environemental usefull utilities
 * such fetching central configuration values or giving error feedback to environment
 *
 * Implementation of these fucntion assume central libs of the applciation are loaded
 * and full generic API is available.
 */

/**
 *
 * Implement :
 *
 * get_archivable_courses()
 *
 * update_course_status($cid, $status)
 *
 */
/**
 * Recycle block external functions
 *
 * @package    block_course_recycle
 * @category   external
 * @copyright  2016 Valery Fremaux
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.2
 */
class block_course_recycle_external extends external_api {


    /* source side Web Services */

    public static function get_archivable_courses_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * retrieves the list of archivable courses for running an archive pull.
     * When the pull is finished, the archive operation will send a status update
     * to the source platform with the original status to perform callback local tasks on course.
     */
    public static function get_archivable_courses() {
        global $DB;

        $sql = "
            SELECT
                c.id,
                c.shortname,
                bcr.status
            FROM
                {course} c,
                {block_course_recycle} bcr
            WHERE
                c.id = bcr.courseid AND
                bcr.status LIKE '%Archive%'
        ";

        $result = array_values($DB->get_records_sql($sql));
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     */
    public static function get_archivable_courses_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'course ID'),
                    'shortname' => new external_value(PARAM_TEXT, 'Course shortname'),
                    'status' => new external_value(PARAM_TEXT, 'Course original status'),
                )
            )
        );
    }

    // Course status

    /**
     * Get course status
     */
    public static function update_course_status_parameters() {
        return new external_function_parameters(
            array(
                'courseidfield' => new external_value(PARAM_TEXT, 'course instance id field. Can be id, shortname or idnumber'),
                'courseid' => new external_value(PARAM_TEXT, 'Course id'),
                'status' => new external_value(PARAM_TEXT, 'New value for status'),
                'postaction' => new external_value(PARAM_TEXT, 'Original value of status, telling some post actions to perform on source side.')
            )
        );
    }

    /**
     * Updates the course status
     * @param string $courseidfield
     * @param int|string $courseid
     * @param string $status
     * @param string $postaction
     */
    public static function update_course_status($courseidfield, $courseid, $status, $postaction = '') {
        global $DB;

        $course = self::validate_course_parameters(self::update_course_status_parameters(),
                        array(
                            'courseidfield' => $courseidfield,
                            'courseid' => $courseid,
                            'status' => $status,
                            'postaction' => $postaction
                        ));

        if (!$rec = $DB->get_record('block_course_recycle', ['courseid' => $course->id])) {
            throw new Exception("The course {$course->id} has no recycle record associated. You cannot invoke this method in this case.");
        }

        $rec->status = $status;
        $DB->update_record('block_course_recycle', $rec);

        if ($status == 'Done') {
            $DB->set_field('block_course_recycle', 'archived', 1, ['id' => $rec->id]);
            $DB->set_field('block_course_recycle', 'timearchived', time(), ['id' => $rec->id]);
            // Launch a $postaction
            if (!empty($postaction)) {
                switch ($postaction) {
                    // Give old status before archive to determine what needs to be done.
                    case "Archive" :
                        break;
                    case "CloneArchiveAndReset" :
                        // Clone the local course.
                        // Reset the cloned course.
                        break;
                    case "ArchiveAndReset" :
                        // Reset the local course.
                        break;
                    case "ArchiveAndDelete" :
                        // Delete the local course.
                        break;
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     */
    public static function update_course_status_returns() {
        return new external_value(PARAM_BOOL, true);
    }

    protected static function validate_course_parameters($configparamdefs, $inputs) {
        global $DB;

        // Standard validation for input data types.
        $status = self::validate_parameters($configparamdefs, $inputs);

        if (!in_array($inputs['courseidfield'], ['id', 'shortname', 'idnumber'])) {
            throw new invalid_parameter_exception('Invalid field for course identity.');
        }

        switch ($inputs['courseidfield']) {
            case 'id' : {
                if (!$course = $DB->get_record('course', ['id' => $inputs['courseid']])) {
                    throw new invalid_parameter_exception('Course does not exist by id.');
                }
                return $course;
            }

            case 'shortname' : {
                if (!$course = $DB->get_record('course', ['shortname' => $inputs['courseid']])) {
                    throw new invalid_parameter_exception('Course does not exist by shortname.');
                }
                return $course;
            }

            case 'idnumber' : {
                if (!$course = $DB->get_record('course', ['idnumber' => $inputs['courseid']])) {
                    throw new invalid_parameter_exception('Course does not exist by idnumber.');
                }
                return $course;
            }
        }
    }

    public static function run_discover_parameters() {
        return new external_function_parameters([]);
    }

    public static function run_discover() {

        $config = get_config('block_course_recycle');

        if ($config->moodletype != 'archive') {
            throw new Exception('The current moodle is not an archive moodle');
        }

        course_recycler::task_discover_finished(false);

        return true;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     */
    public static function run_discover_returns() {
        return new external_value(PARAM_BOOL, true);
    }

    /* Archive Side Web Services */

    public static function run_archive_parameters() {
        return new external_function_parameters([]);
    }

    public static function run_archive() {

        $config = get_config('block_course_recycle');

        if ($config->moodletype != 'archive') {
            throw new Exception('The current moodle is not an archive moodle');
        }

        course_recycler::task_pull_and_archive_courses();

        return true;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     */
    public static function run_archive_returns() {
        return new external_value(PARAM_BOOL, true);
    }

}