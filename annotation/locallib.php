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
        $messagetext = file_save_draft_area_files($draftitemid, $context->id, 'mod_annotation',
                                                    'content', 0, array('subdirs' => true));
    }
    $files = $fs->get_area_files($context->id, 'mod_annotation', 'content', 0, 'sortorder', false);
    if (count($files) == 1) {
        // Only one file attached, set it as main file automatically.
        $file = reset($files);
        file_set_sortorder($context->id, 'mod_annotation', 'content', 0, $file->get_filepath(), $file->get_filename(), 1);
    }

    // Find out the file location by getting the content hash.
    $table = "files";
    $results = $DB->get_records($table, array('itemid' => $draftitemid));
    foreach ($results as $result) {
        $userid = $result->userid;
        $timecreated = $result->timecreated;
        $contenthash = $result->contenthash;
        break; // Bad way of doing it, TODO.
    }

    // Insert a reference into mdl_annotation_document.
    $table = 'annotation_document';
    $record = new stdClass();
    $record->id = 0; // DB will auto increment it.
    $record->userid = $userid;
    $record->group_id = 0;
    $record->time_created = $timecreated;
    $record->documenttype = $data->type;
    $record->location = $contenthash;
    $record->lang = ""; // Lang column no longer used, highlightjs automatically detects language.
    $record->cmid = $cmid;
    $record->groupannotation = $data->groupannotation;
    if (isset($record->groupannotationsvisible)) {
        $record->groupannotationsvisible = $data->groupannotationsvisible;
    } else {
        $record->groupannotationsvisible = 0;
    }
    $record->allowfrom = $data->allowfrom;
    $record->allowuntil = $data->allowuntil;

    $insertid = $DB->insert_record('annotation_document', $record, false);
}

function update_annotation_document($data) {
    global $DB, $CFG;
    $fs = get_file_storage();
    $cmid = $data->coursemodule;

    $table = "annotation_document";
    $context = context_module::instance($cmid);
    $record = $DB->get_records($table);

    $documenttype = $data->type;
    $groupannotation = $data->groupannotation;
    $groupannotationsvisible = $data->groupannotationsvisible;
    $allowfrom = $data->allowfrom;
    $allowuntil = $data->allowuntil;

    $sql = "UPDATE mdl_annotation_document SET documenttype = ?, groupannotation = ?,
                     groupannotationsvisible = ?, allowfrom = ?, allowuntil = ? WHERE cmid = ? ";
    $DB->execute($sql, array($documenttype, $groupannotation, $groupannotationsvisible, $allowfrom, $allowuntil, $cmid));
}

/**
 * Checks time constraints. Returns true if
 * the document is annotatable. Returns false otherwise.
 */
function check_time_constraint($allowfrom, $allowuntil) {
    $currenttime = time();
    if (!$allowfrom && !$allowuntil) {
        return true;
    } else if ($allowfrom && $allowuntil) {
        if ($currenttime < $allowfrom || $currenttime > $allowuntil) {
            return false;
        }
    } else if ($allowfrom) {
        if ($currenttime < $allowfrom) {
            return false;
        }
    } else if ($allowuntil) {
        if ($currenttime > $allowuntil) {
            return false;
        }
    }
    return true;
}
