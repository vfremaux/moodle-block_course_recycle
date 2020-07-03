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
 * This file keeps track of upgrades to the html block
 *
 * @package    block_course_recycle
 * @category   blocks
 * @copyright  Valery Fremaux (valery.fremaux@gmail.com) http://www.mylearningfactory.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 *
 * @param int $oldversion
 * @param object $block
 */
function xmldb_block_course_recycle_upgrade($oldversion) {
    global $CFG, $THEME, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2019061000) { //New version in version.php
        // Define table block_course_recycle to be created.
        $table = new xmldb_table('block_course_recycle');

        // Adding fields to table block_course_recycle.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('status', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('reason', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lastuserid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timearchived', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('stopnotify', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table block_teams_requests.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table block_teams_requests.
        $table->add_index('ix_uniq', XMLDB_INDEX_UNIQUE, array('courseid'));

        // Conditionally launch create table for block_teams_requests.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_block_savepoint(true, 2019061000, 'course_recycle');
    }

    if ($oldversion < 2019092600) { //New version in version.php
        $table = new xmldb_table('block_course_recycle');

        $field = new xmldb_field('status', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);

        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_precision($table, $field);
        }

        upgrade_block_savepoint(true, 2019092600, 'course_recycle');
    }

    if ($oldversion < 2019121600) { //New version in version.php
        // Define table block_course_recycle to be created.
        $table = new xmldb_table('block_course_recycle');

        // Adding fields to table block_course_recycle.
        $field = new xmldb_field('postactions', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'status');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_block_savepoint(true, 2019121600, 'course_recycle');
    }
    return true;
}
