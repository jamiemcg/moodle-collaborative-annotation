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
 * Stores an annotation with the data received from a POST request
 * Must attatch timestamp, id, user_id to the annotation
 * Returns the id of the created annotation
 *
 * @package   mod_annotation
 * @copyright 2015 Jamie McGowan
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!empty($_POST)) {
    require_once(__DIR__ . "../../../../config.php");
    $cmid = $_POST['url'];
    require_once("../initialize.php");
    require_login();

    global $CFG, $DB, $USER;

    if (!$editable) {
        // Time has expired, do not store annotation. Client will reload.
        echo json_encode($editable);
        die();
    }

    if (strlen($_POST['text']) < 1) {
        // No text selected, do not store annotation.
        // Not possible to block client side with annotatorjs?
        die();
    }

    // Use an array to wrap the response data.
    $response = array();
    $response[] = $editable;

    // Create a new object to store the annotation data.
    $annotation = new stdClass();
    $annotation->url = $_POST['url'];
    $annotation->ranges = json_encode($_POST['ranges']);
    $annotation->quote = htmlentities($_POST['quote']);
    $annotation->highlights = json_encode($_POST['highlights']);
    $annotation->annotation = htmlentities($_POST['text']);

    if (isset($_POST['tags']) && !empty($_POST['tags'])) {
        $annotation->tags = json_encode($_POST['tags']);
    }

    $annotation->timecreated = time();
    $annotation->id = 0; // This will be changed by the DB.
    $annotation->userid = $USER->id;
    $annotation->group_id = $group;

    $table = "annotation_annotation";

    // Insert into DB and get the id.

    $lastinsertid = $DB->insert_record($table, $annotation);
    $annotation->id = $lastinsertid;
    $annotation->username = $USER->firstname . " " . $USER->lastname;

    if ($group_annotation) {
        // Gets the name of the group that the current user belongs to.
        $annotation->groupname = groups_get_group_name($group);
    }

    $response[] = $annotation;
    echo json_encode($response);
} else {
    http_response_code(400);
}
