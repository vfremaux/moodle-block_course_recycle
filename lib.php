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

require_once($CFG->dirroot.'/blocks/course_recycle/mailtemplatelib.php');

define('RECYCLE_STAY', 'Stay');
define('RECYCLE_RESET', 'Reset');
define('RECYCLE_RETIRE', 'Retire');
define('RECYCLE_CLONE', 'Clone');
define('RECYCLE_CLONETANDRESET', 'CloneAndReset');
define('RECYCLE_ARCHIVE', 'Archive');
define('RECYCLE_ARCHIVEANDRESET', 'ArchiveAndReset');
define('RECYCLE_ARCHIVEANDDELETE', 'ArchiveAndDelete');
define('RECYCLE_ARCHIVECLONEANDRESET', 'CloneArchiveAndReset');
define('RECYCLE_DELETE', 'Delete');

define('RECYCLE_ASK', 'Ask');
define('RECYCLE_RQFA', 'RequestForArchive');
define('RECYCLE_DONE', 'Done'); // This is an extra
define('RECYCLE_FAILED', 'Failed'); // This is an extra

/**
 * This function is not implemented in this plugin, but is needed to mark
 * the vf documentation custom volume availability.
 * 
 * @param string $feature a cat/item feature key.
 */
function block_course_recycle_supports_feature($feature) {
    assert(1);
}
