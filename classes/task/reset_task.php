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
 * @package   block_course_recycle
 * @category  blocks
 * @author    Valery Fremaux <valery.fremaux@gmail.com>, <valery@edunao.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_course_recycle\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Recycle processing for courses.
 */
class reset_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('task_reset', 'block_course_recycle');
    }

    /**
     * Do the job.
     */
    public function execute() {
        global $DB;

        $config = get_config('block_course_recycle');

        set_config('blockstate', 'inactive', 'block_course_recycle');

        $instances = $DB->get_records('block_instances', array('blockname' => 'course_recycle'));
        if ($instances) {
            foreach ($înstances as $instancerec) {
                $bi = block_instance('course_recycle', $instancerec);
                $bi->config->recycleaction = $config->defaultaction;
                $bi->config->choicedone = false;
                $bi->config->stopnotify = false;
                $bi->instance_config_save();
            }
        }
    }
}