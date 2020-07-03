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
 * This task is used at the archive side.
 * 
 * Synopsys : 
 * -- get the list of pullable courses (courses to archive on the remote side)
 * -- pulls courses and deploy them in archive location
 * -- asynchronously give back status to the remote side when done.
 *
 * @package   block_course_recycle
 * @category  blocks
 * @author    Valery Fremaux <valery.fremaux@gmail.com>, <valery@edunao.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_course_recycle\task;

require_once($CFG->dirroot.'/blocks/course_recycle/locallib.php');
require_once($CFG->dirroot.'/blocks/course_recycle/classes/course_recycler.class.php');

defined('MOODLE_INTERNAL') || die();

use \block_course_recycle\course_recycler;

/**
 * Recycle processing for courses.
 */
class pull_and_archive_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('task_pull_and_archive', 'block_course_recycle');
    }

    /**
     * Do the job.
     */
    public function execute() {
        echo "Calling archiver task\n";
        course_recycler::task_pull_and_archive_courses();
    }
}