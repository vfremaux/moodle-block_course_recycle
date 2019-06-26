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
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$functions = array(

    'block_course_recycle_get_archivable_courses' => array(
        'classname' => 'block_course_recycle_external',
        'methodname' => 'get_archivable_courses',
        'classpath' => 'blocks/course_recycle/externallib.php',
        'description' => 'Returns the list of archivable courses of the local moodle.',
        'type' => 'read',
        'capabilities' => 'block/course_recycle:archive'
    ),

    'block_course_recycle_update_course_status' => array(
        'classname' => 'block_course_recycle_external',
        'methodname' => 'update_course_status',
        'classpath' => 'blocks/course_recycle/externallib.php',
        'description' => 'Updates local course state regarding archive process.',
        'type' => 'write',
        'capabilities' => 'block/course_recycle:archive'
    ),

    'block_course_recycle_run_archive' => array(
        'classname' => 'block_course_recycle_external',
        'methodname' => 'run_archive',
        'classpath' => 'blocks/course_recycle/externallib.php',
        'description' => 'Run the archive process in an archive node.',
        'type' => 'write',
        'capabilities' => 'block/course_recycle:archive'
    ),

    'block_course_recycle_run_discover' => array(
        'classname' => 'block_course_recycle_external',
        'methodname' => 'run_discover',
        'classpath' => 'blocks/course_recycle/externallib.php',
        'description' => 'Run the course ending discovery process in a source node.',
        'type' => 'write',
        'capabilities' => 'block/course_recycle:archive'
    ),

);

$services = array(
    'Archivable Moodle API' => array(
        'functions' => array (
            'block_course_recycle_update_course_status',
            'block_course_recycle_get_archivable_courses',
            'block_course_recycle_run_archive',
            'block_course_recycle_run_discover',
        ),
        'enabled' => 0,
        'restrictedusers' => 1,
        'shortname' => 'block_course_recycle',
        'downloadfiles' => 0,
        'uploadfiles' => 0
    ),
);
