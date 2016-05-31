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
 * Updates the text of an annotation. The annotation
 * is specified by an id number in a POST request.
 * Must attatch new timestamp to updated annotation.
 *
 * @package   mod_annotation
 * @copyright 2015 Jamie McGowan
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!empty($_POST)) {
    require_once(__DIR__ . "../../../../config.php");
    require_login();

    global $CFG, $DB, $USER;

    $id = $_POST['id'];
    $userid = $USER->id;

    $params = array(
                    "id" => $id,
                    "userid" => $userid
                   );

    $table = "annotation_image";
    $count = $DB->count_records($table, $params);
    // If the user logged in didn't create the annotation $count will be 0.
    if ($count) {
        $annotation = htmlentities($_POST['text']);
        $timecreated = time();
        $tags = htmlentities($_POST['tags']);

        $sql = "UPDATE mdl_annotation_image SET annotation = ?, timecreated = ?, tags = ? WHERE id = ? AND userid = ?";
        $DB->execute($sql, array($annotation, $timecreated, $tags, $id, $userid));

        $response = new stdClass();
        $response->id = $id;
        $response->timecreated = $timecreated;
        $response->username = $USER->firstname . " " . $USER->lastname; // Needs to be reset.
        echo json_encode($response); // Return updated object to client.
    } else {
        echo "0";
    }
}
