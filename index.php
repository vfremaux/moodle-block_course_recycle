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
 * Form for editing HTML block instances.
 *
 * @package   block_course_recycle
 * @category  blocks
 * @copyright 1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$url = new moodle_url('/blocks/course_recycle/index.php');
$PAGE->set_url($url);

$context = context_system::instance();
$PAGE->set_context($context);

// Security.

require_login();
require_capability('block/recycle:admin');

$PAGE->set_heading(get_string('pluginname', 'block_course_reclycle'));
$PAGE->set_title(get_string('pluginname', 'block_course_reclycle'));

$countinstances = $DB->count_records('block_instances', array('blockname' => 'course_recycle'));

$pagesize = 40;

$recycleinstances = block_recycle_get_instances($globals);

$renderer = $PAGE->get_renderer('block_course_recycle');

echo $PAGE->header();

echo $renderer->globalstable($globals);

if ($countinstances > $pagesize) {
    echo $OUTPUT->paging_bar($url, optional_param($page), $countinstances);

    echo $renderer->recyclestates($recycleinstances);

    echo $OUTPUT->paging_bar($url, optional_param($page), $countinstances);
}

echo $PAGE->footer();