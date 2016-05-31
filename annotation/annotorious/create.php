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
        // Time has expired, do not store annotation. Send signal (-1) for client to reload..
        echo json_encode($editable);
        die();
    }

    // Use an array to wrap the response data.
    $response = array();
    $response[] = $editable;

    // Create a new object to store the annotation data.
    $annotation = new stdClass();
    $annotation->id = 0; // This will be changed by the DB.
    $annotation->userid = $USER->id;
    $annotation->annotation = htmlentities($_POST['text']);
    $annotation->shapes = json_encode($_POST['shapes']);
    $annotation->url = $_POST['url'];
    $annotation->timecreated = time();
    $annotation->tags = htmlentities($_POST['tags']);
    $annotation->group_id = $group;

    $table = "annotation_image";

    // Insert into DB and get the id.

    $lastinsertid = $DB->insert_record($table, $annotation);
    $annotation->id = $lastinsertid;
    $annotation->username = $USER->firstname . " " . $USER->lastname;
    $annotation->groupname = groups_get_group_name($group);
    // Returns the data to the client for processing.

    // TODO waste of data transfer, only return what is required.
    // E.g. id, username, timecreated...
    $response[] = $annotation;
    echo json_encode($response);
} else {
    http_response_code(400);
}
