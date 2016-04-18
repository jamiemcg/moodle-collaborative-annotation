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
 * Exports all annotation data to an XML file. Restricted to teachers.
 *
 * @package   mod_annotation
 * @category  export
 * @copyright 2015 Jamie McGowan
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (isset($_GET['url']) && isset($_GET['type'])) {
    require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
    require_once(dirname(__FILE__).'/lib.php');
    require_once("$CFG->dirroot/mod/annotation/locallib.php");
    require_login();

    $url = $_GET['url'];
    $document_type = $_GET['type']; // 0 -> plain text; 1 -> source code; 2 -> image.

    $cmid = $url; // Used by initialize.php.
    require_once("initialize.php");

    $cm = get_coursemodule_from_id('annotation', $url, 0, false, MUST_EXIST);

    // Determine if user is student or teacher.
    $context = context_course::instance($cm->course);
    $teacher = has_capability('mod/annotation:manage', $context);

    if (!$teacher) {
        die(); // Blocking export function from students.
    }

    if ($document_type == 2) {
        // Load annotations from db.
        $sql = "SELECT * FROM mdl_annotation_image WHERE url = ?";
        $rs = $DB->get_recordset_sql($sql, array($url));
    } else {
        // Load annotations from db  .
        $sql = "SELECT * FROM mdl_annotation_annotation WHERE url = ?";
        $rs = $DB->get_recordset_sql($sql, array($url));
    }

    // Create XML object.
    $xml = new SimpleXMLElement('<xml/>');
    $annotations = $xml->addChild('annotations');

    // Load comments.
    $sql = "SELECT * FROM mdl_annotation_comment WHERE url = ?";
    $rs_comment = $DB->get_recordset_sql($sql, array($url));

    // Convert comments record set to array for easier processing.
    $comments_array = array();
    foreach ($rs_comment as $record_comment) {
        $comments_array[] = $record_comment;
    }

    // Iterate through the annotations.
    foreach ($rs as $record) {
        // Convert userid to username.
        $user = $DB->get_record('user', array("id" => $record->userid));
        $record->username = $user->firstname . " " . $user->lastname;

        $annotation = $annotations->addChild('annotation');

        if ($document_type != 2) {
            $annotation->addChild('quote', $record->quote);
        } else {
            // Don't have a "quote" for images so process and export the shape, dimensions, etc..
            $shapes = str_replace("[", "", $record->shapes);
            $shapes = str_replace("]", "", $shapes);
            $shapes = json_decode($shapes);

            $selection_shape = $annotation->addChild('selection-shape');
            $selection_shape->addChild('type', $shapes->type);
            $selection_shape->addChild('x', $shapes->geometry->x);
            $selection_shape->addChild('y', $shapes->geometry->y);
            $selection_shape->addChild('width', $shapes->geometry->width);
            $selection_shape->addChild('height', $shapes->geometry->height);

        }

        $annotation->addChild('username', $record->username);
        $annotation->addChild('timecreated', date('Y-m-d H:i:s', $record->timecreated));
        $annotation->addChild('annotation_text', $record->annotation);
        $annotation->addChild('tags', $record->tags);
        // Append comments if any exist.
        $comments = $annotation->addChild('comments');

        for ($i = 0; $i < count($comments_array); $i++) {
            if ($comments_array[$i]->annotation_id == $record->id) {
                $comment = $comments->addChild('comment');

                $user = $DB->get_record('user', array("id" => $comments_array[$i]->user_id));
                $comment->addChild('username', $user->firstname . " " . $user->lastname);
                $comment->addChild('timecreated', date('Y-m-d H:i:s', $comments_array[$i]->timecreated));
                $comment->addChild('comment_text', $comments_array[$i]->comment);
            }
        }
    }

    // Format the XML, should have just used domdocument instead of SimpleXML.
    $dom = dom_import_simplexml($xml)->ownerDocument;
    $dom->formatOutput = true;
    $formatted = $dom->saveXML();

    // Force download of XML file (instead of viewing it).
    header('Content-type: text/xml');
    header('Content-Disposition: attachment; filename="Annotations.xml"');
    header('Content-Length: ' . strlen($xml));
    header('Connection: close');
    print($formatted);
}
