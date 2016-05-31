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
 * Updates an annotation with the data received from a POST request
 * Must attatch new timestamp to the annotation
 * Returns the new annotation object
 *
 * @package   mod_annotation
 * @copyright 2015 Jamie McGowan
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!empty($_POST)) {
    require_once(__DIR__ . "../../../../config.php");
    require_login();

    global $CFG, $DB, $USER;

    $userid = $USER->id;
    $id = $_POST['id'];

    $timecreated = time();
    $annotation = htmlentities($_POST['text']);

    if (isset($_POST['tags']) && !empty($_POST['tags'])) {
        $tags = json_encode($_POST['tags']);
    } else {
        $tags = null; // Uses null for no tags instead of empty string.
    }

    $params = array(
                    "id" => $id,
                    "userid" => $userid
                   );

    $table = "annotation_annotation";
    $count = $DB->count_records($table, $params);
    // If the user logged in didn't create the annotation $count will be 0.
    if ($count) {
        // Save changes to the database.
        $sql = "UPDATE mdl_annotation_annotation SET timecreated = ?, annotation = ?, tags = ? WHERE id = ? AND userid = ?";
        $DB->execute($sql, array($timecreated, $annotation, $tags, $id, $userid));

        // Return the new time.
        echo $timecreated;
    } else {
        echo "0"; // Return error response.
    }
} else {
    http_response_code(400);
}
