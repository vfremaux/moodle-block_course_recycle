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
require_once('test_client_base.php');

class test_client extends test_client_base {

    public function test_get_archivables() {

        if (empty($this->t->baseurl)) {
            echo "Test target not configured\n";
            return;
        }

        if (empty($this->t->wstoken)) {
            echo "No token to proceed\n";
            return;
        }

        $params = array(
            'wstoken' => $this->t->wstoken,
            'wsfunction' => 'block_course_recycle_get_archivable_courses',
            'moodlewsrestformat' => 'json');

        $serviceurl = $this->t->baseurl.$this->t->service;

        return $this->send($serviceurl, $params);
    }

    public function test_update_course_status($field, $id, $status, $postaction) {

        if (empty($this->t->baseurl)) {
            echo "Test target not configured\n";
            return;
        }

        if (empty($this->t->wstoken)) {
            echo "No token to proceed\n";
            return;
        }

        $params = array(
            'wstoken' => $this->t->wstoken,
            'wsfunction' => 'block_course_recycle_update_course_status',
            'moodlewsrestformat' => 'json',
            'courseidfield' => $field,
            'courseid' => $id,
            'status' => $status,
            'postaction' => $postaction);

        $serviceurl = $this->t->baseurl.$this->t->service;

        return $this->send($serviceurl, $params);
    }

}

// Effective test scenario.

echo "STARTING:\n";
$client = new test_client();

echo "GET ARCHIVABLE COURSES:\n";
$client->test_get_archivables();

echo "UPDATE COURSE STATUS:\n";
$client->test_update_course_status('id', 12, 'Done', '');

echo "RESET COURSE STATUS:\n";
$client->test_update_course_status('shortname', 'FINISHED', 'RequestForArchive', '');

echo "UPDATE COURSE STATUS With status Failed:\n";
$client->test_update_course_status('id', 12, 'Failed', '');
die;
echo "RESET COURSE STATUS:\n";
$client->test_update_course_status('shortname', 'FINISHED', 'RequestForArchive', '');
