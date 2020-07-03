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
 * Implement :
 *
 * get_archivable_courses()
 * update_course_status($cid, $status)
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
        return new external_function_parameters([
            'alldates' => new external_value(PARAM_BOOL, 'All dates required', VALUE_OPTIONAL)
        ]);
    }

    /**
     * retrieves the list of archivable courses for running an archive pull.
     * When the pull is finished, the archive operation will send a status update
     * to the source platform with the original status to perform callback local tasks on course.
     * @params bool $alldates true if all dates are required, even if not processable NOW.
     */
    public static function get_archivable_courses($alldates = false) {
        global $DB;

        $config = get_config('block_course_recycle');

        $params = [];

        $delayedactionclause = '';
        $processable = '1 as processable';
        $timeprocessabledelay = 0 + @$config->actiondelay * DAYSECS;
        if (!empty($config->actiondelay) && !$alldates) {
            $delayedactionclause = " AND bcr.timemodified < ? ";
            $processabledate = time() - ($config->actiondelay * DAYSECS);
            $params[] = $processabledate;
            $processable = "(bcr.timemodified < $processabledate) as processable ";
        }

        $sql = "
            SELECT
                c.id,
                c.shortname,
                c.fullname,
                c.idnumber,
                bcr.status,
                bcr.timemodified,
                bcr.timemodified + {$timeprocessabledelay} as timeprocessable,
                {$processable}
            FROM
                {course} c,
                {block_course_recycle} bcr
            WHERE
                c.id = bcr.courseid AND
                bcr.status LIKE '%Archive%' AND
                bcr.status != 'RequestForArchive'
                {$delayedactionclause}
        ";

        $archivables = $DB->get_records_sql($sql, $params);
        if (empty($archivables)) {
            return [];
        }

        // Compute the full path category.
        foreach (array_keys($archivables) as $aid) {
            $catpathelms = [];
            // Get full categorypath for the course and add it to record.
            $cat = $DB->get_record('course_categories', ['id' => $a->category], 'id,parent,name');
            array_unshift($catpathelms, $cat->name);
            while ($parent = $cat->parent) {
                $cat = $DB->get_record('course_categories', ['id' => $a->category], 'id,parent,name');
                array_unshift($catpathelms, $cat->name);
            }
            $archivables[$aid]->sourcecategorypath = implode('/', $catpathelms);
        }

        $result = array_values($archivables);

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
                    'fullname' => new external_value(PARAM_TEXT, 'Course fullname'),
                    'sourcecategorypath' => new external_value(PARAM_TEXT, 'Course full names category slashed path'),
                    'idnumber' => new external_value(PARAM_TEXT, 'Course idnumber'),
                    'status' => new external_value(PARAM_TEXT, 'Course original status'),
                    'timemodified' => new external_value(PARAM_INT, 'Last change date'),
                    'timeprocessable' => new external_value(PARAM_INT, 'Time for processing action'),
                    'processable' => new external_value(PARAM_BOOL, 'Is processable for archiving'),
                )
            )
        );
    }

    // Course status

    /**
     * Get course status parameters
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
     * Updates the course status and processes locally to the postactions.
     * @param string $courseidfield
     * @param int|string $courseid
     * @param string $status
     * @param string $postaction
     */
    public static function update_course_status($courseidfield, $courseid, $status, $postaction = '') {
        global $DB, $USER;

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

        $rec->postactions = $postactions;

        $rec->status = $status;
        if (preg_match('/Archive/', $postaction)) {
            $rec->timearchived = time();
            $rec->lastuserid = $USER->id;
        }
        $DB->update_record('block_course_recycle', $rec);

        if ($status == 'Done') {
            $DB->set_field('block_course_recycle', 'timearchived', time(), ['id' => $rec->id]);
            if (!empty($postaction)) {
                course_recycler::process_action($course, $postaction);
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