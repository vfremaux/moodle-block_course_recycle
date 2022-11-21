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

define('RECYCLE_STAY', 'Stay'); // Action state : Do nothing.
define('RECYCLE_RESET', 'Reset'); // Action state.
define('RECYCLE_RETIRE', 'Retire'); // Action state.
define('RECYCLE_CLONE', 'Clone'); // Action state.
define('RECYCLE_CLONEANDRESET', 'CloneAndReset'); // Action state.
define('RECYCLE_ARCHIVE', 'Archive'); // Action state.
define('RECYCLE_ARCHIVEANDRESET', 'ArchiveAndReset'); // Action state.
define('RECYCLE_ARCHIVEANDDELETE', 'ArchiveAndDelete'); // Action state.
define('RECYCLE_ARCHIVECLONEANDRESET', 'ArchiveCloneAndReset'); // Action state.
define('RECYCLE_DELETE', 'Delete'); // Action state.
define('RECYCLE_FREEZE', 'Freeze'); // Action state. Prov for M3.9
define('RECYCLE_FREEZECLONEANDRESET', 'Freeze'); // Action state. Prov for M3.9

define('RECYCLE_ASK', 'Ask'); // PseudoAction state

define('RECYCLE_DONE', 'Done'); // Result states : All finished
define('RECYCLE_FAILED', 'Failed'); // Result states : Failure
define('RECYCLE_ARCHIVED', 'Archived'); // Result states : Archived in remote node.

/**
 * This function is not implemented in this plugin, but is needed to mark
 * the vf documentation custom volume availability.
 * 
 * @param string $feature a cat/item feature key.
 */
function block_course_recycle_supports_feature($feature) {
    assert(1);
}
