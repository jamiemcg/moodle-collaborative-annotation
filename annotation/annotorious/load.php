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
 * Loads the annotations from the server for the image currently being viewed.
 * POST request with the window.location.url (cmid)
 *
 * @package   mod_annotation
 * @copyright 2015 Jamie McGowan
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!empty($_POST['url'])) {
    require_once(__DIR__ . "../../../../config.php");
    require_login();

    global $CFG, $DB, $USER;

    $userid = $USER->id; // Gets the current users id.

    $table = 'annotation_image';
    $url = $_POST['url'];
    $cmid = $url;

    require_once("../initialize.php");

    $cm = get_coursemodule_from_id('annotation', $url, 0, false, MUST_EXIST);

    // Determine if the user is a teacher or a student.
    $context = context_course::instance($cm->course);
    $teacher = has_capability('mod/annotation:manage', $context);

    // Check if group annotations are enabled and if group visibility is enabled.
    if ($groupannotation && $teacher) {
        $sql = "SELECT * FROM mdl_annotation_image WHERE url = ?";
        $rs = $DB->get_recordset_sql($sql, array($url));
    } else if ($groupannotation && ! $groupannotationsvisible) {
        $sql = "SELECT * FROM mdl_annotation_image WHERE url = ? AND (group_id = ? OR group_id = -1)";
        $rs = $DB->get_recordset_sql($sql, array($url, $group));
    } else {
        // Group annotation is disabled, load all existing annotations.
        $sql = "SELECT * FROM mdl_annotation_image WHERE url = ?";
        $rs = $DB->get_recordset_sql($sql, array($url));
    }

    $response = array();

    $response[] = $editable; // Testing, block annotations if they are disabled.

    // Loop through results.
    foreach ($rs as $record) {
        // Get username of annotation creator.
        $user = $DB->get_record('user', array("id" => $record->userid));
        $record->username = $user->firstname . " " . $user->lastname;
        if ($groupannotation) {
            $record->groupname = groups_get_group_name($record->group_id);
        }
        // Enable editing of annotation only if current user created it.
        if ($record->userid == $userid) {
            $record->editable = true;
        } else {
            $record->editable = false;
        }

        $response[] = $record;
    }
    $rs->close(); // Close the record set.

    echo json_encode($response);
} else {
    http_response_code(400);
}
