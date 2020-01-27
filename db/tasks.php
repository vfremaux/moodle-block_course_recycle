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
 * Definition of block dashboard scheduled tasks.
 *
 * @package   block_course_recycle
 * @category  blocks
 * @author    Valery Fremaux <valery.fremaux@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$tasks = array(
    array(
        'classname' => 'block_course_recycle\task\recycle_task',
        'blocking' => 0,
        'minute' => '*',
        'hour' => '*',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
        'disabled' => 1,
    ),

    array(
        'classname' => 'block_course_recycle\task\lock_task',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '0',
        'day' => '15',
        'month' => '7',
        'dayofweek' => '*',
        'disabled' => 1,
    ),

    array(
        'classname' => 'block_course_recycle\task\reset_task',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '0',
        'day' => '2',
        'month' => '9',
        'dayofweek' => '*',
        'disabled' => 1,
    ),

    array(
        'classname' => 'block_course_recycle\task\show_task',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '0',
        'day' => '15',
        'month' => '5',
        'dayofweek' => '*',
        'disabled' => 1,
    ),

    array(
        'classname' => 'block_course_recycle\task\discover_finished_courses_task',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '2',
        'day' => '20',
        'month' => '*',
        'dayofweek' => '*',
        'disabled' => 1,
    ),

    array(
        'classname' => 'block_course_recycle\task\pull_and_archive_task',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '2',
        'day' => '30',
        'month' => '*',
        'dayofweek' => '*',
        'disabled' => 1,
    ),
);
