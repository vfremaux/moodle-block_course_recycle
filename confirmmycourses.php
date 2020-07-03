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
 * Allows a user to confirm backup of his courses.
 *
 * @package   block_course_recycle
 * @category  blocks
 * @copyright 1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require('../../config.php');
require_once($CFG->dirroot.'/blocks/course_recycle/locallib.php');
require_once($CFG->dirroot.'/blocks/course_recycle/classes/course_recycler.class.php');

use \block_course_recycle\course_recycler;

$id = optional_param('fromcourse', SITEID, PARAM_INT);
$topcatid = optional_param('topcatid', 0, PARAM_INT);
$page = optional_param('page', 1, PARAM_INT);

$url = new moodle_url('/blocks/course_recycle/confirmmycourses.php', ['id' => $id, 'topcatid' => $topcatid]);
$PAGE->set_url($url);

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->requires->jquery();
$PAGE->requires->js_call_amd('block_course_recycle/recyclelist', 'init');
$PAGE->requires->css('/blocks/course_recycle/css/bootstrap-select.css');

// Security.

require_login();
require_capability('moodle/site:config', $context);

$PAGE->set_heading(get_string('confirmmycourses', 'block_course_recycle'));
$PAGE->set_title(get_string('pluginname', 'block_course_recycle'));

$pagesize = 20;

$renderer = $PAGE->get_renderer('block_course_recycle');

$authoredcourses = array();
$capability = 'moodle/course:manageactivities';

if (!has_capability('moodle/site:config', $context)) {
    $authoredcourses = block_course_recycle_get_user_capability_course($capability, $USER->id, false, '', 'cc.sortorder, c.sortorder');
    $authoredcourseids = array_keys($authoredcourses);
} else {
    // Get all courses in and below a top category.
    if ($topcatid) {
        $catpath = $DB->get_field('course_categories', 'path', ['id' => $topcatid]);
        $select = $DB->sql_like('path', ':path');
        $catcourses = $DB->get_records('course', ['category', $topcatid]);
        $allcourses = $DB->get_records_select('course', ['path' => $catpath.'/%']);
        if ($catcourses && $allcourses) {
            $allcourses = $catcourses + $allcourses;
        } else if ($catcourses) {
            $allcourses = $catcourses;
        }
    } else {
        // Get all courses
        $allcourses = $DB->get_records('course');
    }
    $authoredcourseids = array_keys($allcourses);
}

$mycandidatecourses = course_recycler::get_my_candidate($authoredcourseids, $page, $pagesize, $totalcount);

if ($totalcount > $pagesize) {
    $template->pager = $OUTPUT->paging_bar($totalcount, $page - 1 , $pagesize, $url, 'page');
}

// Start print page.

echo $OUTPUT->header();

if (has_capability('moodle/site:config', $context)) {
    echo $renderer->category_filter($id, $topcatid);
}

echo $renderer->confirm_table($mycandidatecourses);

if (has_capability('moodle/site:config', $context)) {
    $buttonurl = new moodle_url('/blocks/course_recycle/detect.php', array('courseid' => $id));
    echo $OUTPUT->single_button($buttonurl, get_string('detectcourses', 'block_course_recycle'));
}

if ($id) {
    $returnurl = new moodle_url('/course/view.php', ['id' => $id]);
    $label = get_string('backtocourse', 'block_course_recycle');
} else {
    $returnurl = $CFG->wwwroot;
    $label = get_string('backtodashboard', 'block_course_recycle');
}
echo '<center>';
echo $OUTPUT->single_button($returnurl, $label);
echo '</center>';

echo $OUTPUT->footer();