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
 * Receive a comment based on commentid in POST request
 * Checks if the current user created the corresponding comment
 *
 * @package   mod_annotation
 * @copyright 2015 Jamie McGowan
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!empty($_POST['id'])) {
    require_once(__DIR__ . "../../../../config.php");
    require_login();

    global $CFG, $DB, $USER;

    $userid = $USER->id; // Gets the current users id.
    $commentid = $_POST['id']; // The id of the comment.

    $params = array(
                    "id" => $commentid,
                    "userid" => $userid
                   );

    $table = "annotation_comment";
    $count = $DB->count_records($table, $params);

    // If the user logged in didn't create the comment $count will be 0.
    if ($count) {
        $result = $DB->delete_records($table, $params);
        echo "1"; // Return success response (i.e. comment is deleted).
    } else {
        echo "0"; // Return failure response (user didn't create the comment).
    }
} else {
    http_response_code(400);
}
