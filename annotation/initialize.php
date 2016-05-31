<?php
// This file is part of mod_annotation
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
 * Determines the current users group and time restriction settings.
 *
 * @package   initialize
 * @copyright 2015 Jamie McGowan
 * @license   http:// www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

require_login();

global $CFG, $DB, $USER;
$cm = get_coursemodule_from_id('annotation', $cmid, 0, false, MUST_EXIST);
$context = context_course::instance($cm->course);
$teacher = has_capability('mod/annotation:manage', $context);

// Determine group settings.
$table = "annotation_document";
$results = $DB->get_records($table, array('cmid' => $cmid));
foreach ($results as $result) {
        $group_annotation = $result->group_annotation;
        $group_annotations_visible = $result->group_annotations_visible;
        $allow_from = $result->allow_from;
        $allow_until = $result->allow_until;
        break; // Bad way of doing this.
}

// Determine the user's group even if group mode is disabled.
$group = groups_get_user_groups($cm->course, $USER->id);
if (count($group[0]) > 0) { // User may not have a group? E.g teacher?
    $group = $group[0][0];
} else {
    $group = -1; // Set to -1 if teacher, or if group undefined.
}

// Determine availability settings.
$editable = check_time_constraint($allow_from, $allow_until);
