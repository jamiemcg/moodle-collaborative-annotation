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
 * Loads comments from the server for the annotation_activity
 * Responds to a POST request with the url -> cmid
 *
 * @package   mod_annotation
 * @copyright 2015 Jamie McGowan
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!empty($_POST['url'])) {
    require_once(__DIR__ . "../../../../config.php");
    require_once("$CFG->dirroot/mod/annotation/locallib.php");
    require_login();

    global $CFG, $DB, $USER;

    $userid = $USER->id; // Gets the current users id.
    $url = $_POST['url'];

    // Select all comments where the url matches that posted.
    $sql = "SELECT * FROM mdl_annotation_comment WHERE url = ?";
    $rs = $DB->get_recordset_sql($sql, array($url));

    $response = array(); // Store the response in an array.

    foreach ($rs as $record) {
        $user = $DB->get_record('user', array("id" => $record->userid));
        $record->username = $user->firstname . " " . $user->lastname;

        unset($record->userid); // Don't send the user's id to the client.

        $response[] = $record; // Apend the record to the response array.
    }

    $rs->close(); // Close the record set.
    echo json_encode($response); // Return the comments.
} else {
    http_response_code(400);
}
