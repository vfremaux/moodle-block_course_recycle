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
 * @package     block_course_recycle
 * @category    blocks
 * @author      Valery Fremaux (valery.fremaux@gmail.com)
 * @copyright   1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

function block_course_recycle_get_instances(&$globals) {
    global $DB;

    $page = optional_param('page', 0, PARAM_INT);

    $sql = "
        SELECT
            c.shortname,
            c.fullname,
            c.idnumber,
            bi.configdata
        FROM
            {course} c,
            {context} ctx,
            {block_instances} bi
        WHERE
            c.id = ctx.instanceid AND
            ctx.contextlevel = 50 AND
            ctx.id = bi.parentcontextid AND
            bi.blockname = 'course_recycle'
    ";

    $instances = $DB->get_records_sql($sql, array());

    $globalconfig = get_config('block_course_recycle');

    $globals['throw'] = 0;
    $globals['archive'] = 0;
    $globals['keep'] = 0;
    $globals['reset'] = 0;
    $globals['unset'] = 0;
    foreach ($instances as $id => $instance) {
        $config = unserialize(base64_decode($instance->configdata));

        if ($config) {
            $recycleaction = $config->recycleaction;
        } else {
            $recycleaction = $globalconfig->defaultaction;
        }

        $instances[$id]->recycle = $recycleaction;

        switch ($recycleaction) {
            case 'throw': {
                $globals['throw']++;
                break;
            }

            case 'archive': {
                $globals['archive']++;
                break;
            }

            case 'keep': {
                $globals['keep']++;
                break;
            }

            case 'reset': {
                $globals['reset']++;
                break;
            }

            default:
                $globals['unset']++;
        }
    }

    return $instances;
}

function block_course_recycle_add_to_courses() {
    global $DB;

    $allcourses = $DB->get_records('course');

    if ($allcourses) {
        $added = false;
        foreach ($allcourses as $course) {
            $context = context_course::instance($course->id);
            $params = array('parentcontextid' => $context->id, 'blockname' => 'course_recycle');
            if (!$recycleinstance = $DB->get_records('block_instances', $params)) {
                if ($course->format != 'page' && $course->format != 'singleactivity') {
                    mtrace('Adding course recycle instance to course ['.$course->id.'] '.$course->fullname);
                    block_recycle_add_block_to_course($context, 'side-post', '-9');
                    $added = true;
                } else if ($course->format == 'page') {
                    // Special processing.
                    // TODO : finish.
                }
            }
        }

        if (!$added) {
            mtrace('No course needed a course_recycle block');
        }

    } else {
        mtrace('No courses');
    }
}

function block_recycle_add_block_to_course($context, $region, $weight, $showinsubcontexts = false, $pagetypepattern = NULL, $subpagepattern = NULL) {
    global $DB;

    if (empty($pagetypepattern)) {
        $pagetypepattern = '*';
    }

    $blockinstance = new stdClass;
    $blockinstance->blockname = 'course_recycle';
    $blockinstance->parentcontextid = $context->id;
    $blockinstance->showinsubcontexts = !empty($showinsubcontexts);
    $blockinstance->pagetypepattern = $pagetypepattern;
    $blockinstance->subpagepattern = $subpagepattern;
    $blockinstance->defaultregion = $region;
    $blockinstance->defaultweight = $weight;
    $blockinstance->configdata = '';
    $blockinstance->id = $DB->insert_record('block_instances', $blockinstance);

    // Ensure the block context is created.
    context_block::instance($blockinstance->id);
}
