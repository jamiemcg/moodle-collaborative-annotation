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
 * Stores an comment with the data received from a POST request
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

    if (strlen($_POST['comment']) < 1) {
        // No text selected, do not store annotation.
        // Also blocked client side so should never run.
        die();
    }

    $comment = new stdClass();
    $comment->url = $_POST['url'];
    $comment->annotation_id = $_POST['annotation_id'];
    $comment->timecreated = time();
    $comment->comment = htmlentities($_POST['comment']);
    $comment->user_id = $USER->id;
    $comment->id = 0; // This will be changed by the DB.

    $table = "annotation_comment";
    // Insert into DB and get the id.
    $lastinsertid = $DB->insert_record($table, $comment);

    // Send the user name and timecreated back to the client.
    // Also return comment's id for completeness (may be used in future).
    $response = new stdClass();
    $response->username = $USER->firstname . " " . $USER->lastname;
    $response->timecreated = $comment->timecreated;
    $response->id = $lastinsertid;
    echo json_encode($response);
} else {
    http_response_code(400);
}
