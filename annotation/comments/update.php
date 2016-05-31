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
 * Updates a comment with data receieve from POST request
 * POST request contains: (comment) id, comment, url (url needed?)
 * Timestamp (timecreated) should be updated
 * New information is returned to client
 *
 * @package   mod_annotation
 * @copyright 2015 Jamie McGowan
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!empty($_POST)) {
    require_once(__DIR__ . "../../../../config.php");
    require_login();

    global $CFG, $DB, $USER;

    $user_id = $USER->id;
    $id = $_POST['id']; // The comment id.
    $comment = htmlentities($_POST['comment']);

    $timecreated = time(); // New time, timecreated isn't a good name.

    $params = array("id" => $id, "user_id" => $user_id);
    $table = "annotation_comment";
    $count = $DB->count_records($table, $params);

    // Count will be TRUE if the current user created the original comment.
    if ($count) {
        $sql = "UPDAT mdl_annotation_comment SET timecreated = ?, comment = ? WHERE id = ? AND user_id = ?";
        $DB->execute($sql, array($timecreated, $comment, $id, $user_id));

        echo $timecreated; // Return the new time.
    } else {
        echo "0"; // Return error response (not successful or else wrong user).
    }
} else {
    http_response_code(400);
}
