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
 * @copyright   2013 onwards Valery Fremaux (http://www.mylearningfactory.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

function block_recycle_get_instances(&$globals) {
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

    $globals['throw']  = 0;
    $globals['keep'] = 0;
    $globals['reset']  = 0;
    $globals['unset']  = 0;
    foreach ($instances as $id => $instance) {
        $config = unserialize(base64_decode($instance->configdata));
        $instances[$id]->recycle = $config->recycleaction;
        switch ($config->recycleaction) {
            case 'throw': $globals['throw']++ ; break;
            case 'keep': $globals['keep']++ ; break;
            case 'reset': $globals['reset']++ ; break;
            default: $globals['unset']++ ; break;
        }
    }

    return $instances;
}
