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
 * @package block
 * @category course_recycle
 * @author Valery Fremaux
 *
 * Library of functions for mail templating
 * these functions may be redundant with other plugins mail templating
 * but copied here for modularity enhancement.
 */

/**
 * useful templating functions from an older project of mine, hacked for Moodle
 * @param string $template the template's file name from $CFG->sitedir
 * @param array $infomap a hash containing pairs of parm => data to replace in template
 * @param string $module themodule where to find the template tpl files
 * @param text $alternatetemplate when provided, this content will override the file hardlinked template. 
 * @return a fully resolved template where all data has been injected
 */
function block_course_recycle_compile_mail_template($template, $infomap, $lang = null) {
    global $USER;

    if (!$lang) {
        $lang = $USER->lang;
    }

    // TODO : Solve lang
   $notification = get_string($template, 'block_course_recycle');

    foreach ($infomap as $akey => $avalue) {
        $notification = str_replace("<%%$akey%%>", $avalue, $notification);
    }

    return $notification;
}