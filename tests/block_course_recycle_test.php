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
 * Block Tag Youtube tests
 *
 * @package    block_tag_youtube
 * @category   test
 * @copyright  2015 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/blocks/course_recycle/classes/course_recycler.class.php');
require_once($CFG->dirroot . '/mod/forum/lib.php');

use block_course_recycle\course_recycler;

/**
 * Block Tag Youtube test class.
 *
 * @package   block_course_recycle
 * @category  test
 * @copyright 2020 Valery Fremaux
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_course_recycle_testcase extends advanced_testcase {

    /**
     * Test setUp.
     */
    public function setUp() {
        global $DB;

        $this->setAdminUser();
        $this->resetAfterTest(true);

        // We must clear the subscription caches. This has to be done both before each test, and after in case of other
        // tests using these functions.
        \mod_forum\subscriptions::reset_forum_cache();
        \mod_forum\subscriptions::reset_discussion_cache();
    }

    /**
     * Testing the tag youtube block's initial state after a new installation.
     *
     * @return void
     */
    public function test_primitive_retire() {
        global $DB;

        $this->resetAfterTest(true);

        $config = get_config('block_course_recycle');

        if (!empty($config->retirecategory)) {
            $category = $this->getDataGenerator()->create_category();
            $params = array('name' => 'Test course to retire', 'shortname' => 'TESTCOURSERECYCLE', 'category' => $category->id);
            $course = $this->getDataGenerator()->create_course($params);

            course_recycler::process_postactions($course, 'Retire');

            $destcat = $DB->get_field('course', 'category', ['id' => $courseid]);
            $this->assertTrue($descat == $config->retirecategory);
        }
    }

    public function test_primitive_delete() {
        global $DB;

        $this->resetAfterTest(true);

        $category = $this->getDataGenerator()->create_category();
        $params = array('name' => 'Test course to retire', 'shortname' => 'TESTCOURSERECYCLE', 'category' => $category->id);
        $course = $this->getDataGenerator()->create_course($params);

        course_recycler::process_postactions($course, 'Delete');

        $this->assertTrue(!$DB->record_exists('course', ['id' => $course->id]));
    }

    public function test_primitive_clone() {
        global $DB;

        $this->resetAfterTest(true);

        $category = $this->getDataGenerator()->create_category();
        $shortname = 'TESTCOURSERECYCLE';
        $params = array('name' => 'Test course to retire', 'shortname' => $shortname, 'idnumber' => 'TESTCOURSDERECYCLEIDN', 'category' => $category->id);
        $course = $this->getDataGenerator()->create_course($params);

        $newshortname = course_recycler::get_cloned_shortname($course->shortname);
        $newidnumber = course_recycler::get_cloned_idnumber($course->idnumber);
        course_recycler::process_postactions($course, 'Clone');

        $this->assertTrue($DB->record_exists('course', ['shortname' => $newshortname]));
        $this->assertTrue($DB->record_exists('course', ['idnumber' => $newidnumber]));
    }

    public function test_primitive_reset() {
        global $DB;

        $this->resetAfterTest(true);

        // User that will create the forum.
        $user = self::getDataGenerator()->create_user();

        // Create course to add the forum to.
        $course = self::getDataGenerator()->create_course();

        // The forum.
        $record = new stdClass();
        $record->course = $course->id;
        $forum = self::getDataGenerator()->create_module('forum', $record);

        // Add a few discussions.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $record['pinned'] = FORUM_DISCUSSION_PINNED; // Pin one discussion.
        self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);
        $record['pinned'] = FORUM_DISCUSSION_UNPINNED; // No pin for others.
        self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);
        self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Check the discussions were correctly created.
        $this->assertEquals(3, $DB->count_records_select('forum_discussions', 'forum = :forum',
            array('forum' => $forum->id)));

        $record['tags'] = array('Cats', 'mice');
        $record = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);
        $this->assertEquals(array('Cats', 'mice'),
            array_values(core_tag_tag::get_item_tags_array('mod_forum', 'forum_posts', $record->firstpost)));

        course_recycler::process_postactions($course, 'Reset');

        // Check discussions are gone.
        $this->assertEquals(0, $DB->count_records_select('forum_discussions', 'forum = :forum',
            array('forum' => $forum->id)));

    }
}
