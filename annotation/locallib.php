<?php

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
 * Internal library of functions for module annotation
 *
 * All the annotation specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    mod_annotation
 * @copyright  2015 Jamie McGowan
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/*
 * Does something really useful with the passed things
 *
 * @param array $things
 * @return object
 *function annotation_do_something_useful(array $things) {
 *    return new stdClass();
 *}
 */

function store_annotation_document($data) {
    global $DB, $CFG;
    $fs = get_file_storage();
    $cmid = $data->coursemodule;
    $draftitemid = $data->files;

    $context = context_module::instance($cmid);
    if ($draftitemid) {
        $messagetext = file_save_draft_area_files($draftitemid, $context->id, 'mod_annotation', 'content', 0, array('subdirs'=>true));
    }
    $files = $fs->get_area_files($context->id, 'mod_annotation', 'content', 0, 'sortorder', false);
    if (count($files) == 1) {
        // only one file attached, set it as main file automatically
        $file = reset($files);
        file_set_sortorder($context->id, 'mod_annotation', 'content', 0, $file->get_filepath(), $file->get_filename(), 1);
    }
   
    //Find out the file location by getting the content hash
    $table = "files";
    $results = $DB->get_records($table, array('itemid' => $draftitemid));
    foreach ($results as $result) {
        $userid = $result->userid;
        $timecreated = $result->timecreated;
        $contenthash = $result->contenthash;
        break; //Bad way of doing it, TODO
    }

    //Insert a reference into mdl_annotation_document
    $table = 'annotation_document';
    $record = new stdClass();
    $record->id = 0; //should auto increment it
    $record->user_id = $userid;
    $record->group_id = 0;
    $record->time_created = $timecreated;
    $record->document_type = $data->type;
    $record->location = $contenthash;
    $record->lang = ""; //TODO : remove lang field because of automatic detection?
    $record->cmid = $cmid;
    $record->group_annotation = $data->group_annotation;
    if(isset($record->group_annotations_visible)) {
        $record->group_annotations_visible = $data->group_annotations_visible;
    }
    else {
        $record->group_annotations_visible = 0;
    }
    $record->allow_from = $data->allow_from;
    $record->allow_until = $data->allow_until;

    $insertid = $DB->insert_record('annotation_document', $record, false);
}

function update_annotation_document($data) {
    global $DB, $CFG;
    $fs = get_file_storage();
    $cmid = $data->coursemodule;

    $table = "annotation_document";
    $context = context_module::instance($cmid);
    $record = $DB->get_records($table);

    $document_type = $data->type;
    $group_annotation = $data->group_annotation;
    $group_annotations_visible = $data->group_annotations_visible;
    $allow_from = $data->allow_from;
    $allow_until = $data->allow_until;
    
    $sql = "UPDATE mdl_annotation_document SET document_type = ?, group_annotation = ?, group_annotations_visible = ?, allow_from = ?, allow_until = ? WHERE cmid = ? ";
    $DB->execute($sql, array($document_type, $group_annotation, $group_annotations_visible, $allow_from, $allow_until, $cmid));
}

/**
 * Checks time constraints. Returns true if
 * the document is annotatable. Returns false otherwise.
 * TODO:    This function is now redundant and has been replaced
 *          in initialize.php
 */
function check_time_constraint($allow_from, $allow_until) {
    $current_time = time();
    if(!$allow_from && !$allow_until) {
        return true;
    }
    else if($allow_from && $allow_until) {
        if($current_time < $allow_from || $current_time > $allow_until) {
            return false;
        }
    }
    else if($allow_from) {
        if($current_time < $allow_from) {
            return false;
        }
    }
    else if($allow_until) {
        if($current_time > $allow_until) {
            return false;
        }
    }
    return true;
}
