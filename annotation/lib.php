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
 * Library of interface functions and constants for module annotation
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 *
 * All the annotation specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod_annotation
 * @copyright  2015 Jamie McGowan
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/* Moodle core API */

/**
 * Returns the information on whether the module supports a feature
 *
 * See {@link plugin_supports()} for more info.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function annotation_supports($feature) {

    switch($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        default:
            return null;
    }
}


/**
 * Saves a new instance of the annotation into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $annotation Submitted data from the form in mod_form.php
 * @param mod_annotation_mod_form $mform The form instance itself (if needed)
 * @return int The id of the newly inserted annotation record
 */
function annotation_add_instance(stdClass $annotation, mod_annotation_mod_form $mform = null) {
    global $CFG, $DB, $USER;
    require_once("$CFG->dirroot/mod/annotation/locallib.php");
    $annotation->timecreated = time();
    $annotation->id = $DB->insert_record('annotation', $annotation);

    annotation_grade_item_update($annotation); // Permanently store the file and add record to DB.
    store_annotation_document($annotation);
    return $annotation->id;
}

/**
 * Updates an instance of the annotation in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $annotation An object from the form in mod_form.php
 * @param mod_annotation_mod_form $mform The form instance itself (if needed)
 * @return boolean Success/Fail
 */
function annotation_update_instance(stdClass $annotation, mod_annotation_mod_form $mform = null) {
    global $DB, $CFG;
    require_once("$CFG->dirroot/mod/annotation/locallib.php");
    $annotation->timemodified = time();
    $annotation->id = $annotation->instance;

    $result = $DB->update_record('annotation', $annotation);

    annotation_grade_item_update($annotation);

    update_annotation_document($annotation);
    return $result;
}

/**
 * Removes an instance of the annotation from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function annotation_delete_instance($id) {
    global $DB;

    if (! $annotation = $DB->get_record('annotation', array('id' => $id))) {
        return false;
    }

    $cm = get_coursemodule_from_instance('annotation', $annotation->id, $course->id, false, MUST_EXIST);
    $cmid = $cm->id;

    // Delete the associated annotations.
    // Determine DOC type.
    $table = "annotation_document";
    $results = $DB->get_records($table, array('cmid' => $cmid));
    foreach ($results as $result) {
            $contenthash = $result->location;
            $documenttype = $result->documenttype;
            break; // Bad way of doing this.
    }

    if ($documenttype == 2) {
        // Doc is an image.
        $sql = "DELETE FROM mdl_annotation_image WHERE url=?";
    } else {
        // Doc is a text file/source code.
        $sql = "DELETE FROM mdl_annotation_annotation WHERE url=?";
    }

    $DB->execute($sql, array($cmid));

    // Delete the comments created under any annotations for this activity.
    $sql = "DELETE FROM mdl_annotation_comment WHERE url=?";
    $DB->execute($sql, array($cmid));

    $DB->delete_records('annotation', array('id' => $annotation->id));
    $DB->delete_records('annotation_document', array('cmid' => $cmid));
    annotation_grade_item_delete($annotation);

    $context = context_module::instance($cm->id);
    $fs = get_file_storage();
    $fs->delete_area_files($context->id);

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 *
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @param stdClass $course The course record
 * @param stdClass $user The user record
 * @param cm_info|stdClass $mod The course module info object or record
 * @param stdClass $annotation The annotation instance record
 * @return stdClass|null
 */
function annotation_user_outline($course, $user, $mod, $annotation) {

    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * It is supposed to echo directly without returning a value.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $annotation the module instance record
 */
function annotation_user_complete($course, $user, $mod, $annotation) {
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in annotation activities and print it out.
 *
 * @param stdClass $course The course record
 * @param bool $viewfullnames Should we display full names
 * @param int $timestart Print activity since this timestamp
 * @return boolean True if anything was printed, otherwise false
 */
function annotation_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link annotation_print_recent_mod_activity()}.
 *
 * Returns void, it adds items into $activities and increases $index.
 *
 * @param array $activities sequentially indexed array of objects with added 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 */
function annotation_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item prepared by {@link annotation_get_recent_mod_activity()}
 *
 * @param stdClass $activity activity record with added 'cmid' property
 * @param int $courseid the id of the course we produce the report for
 * @param bool $detail print detailed report
 * @param array $modnames as returned by {@link get_module_types_names()}
 * @param bool $viewfullnames display users' full names
 */
function annotation_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 *
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * Note that this has been deprecated in favour of scheduled task API.
 *
 * @return boolean
 */
function annotation_cron () {
    return true;
}

/**
 * Returns all other caps used in the module
 *
 * For example, this could be array('moodle/site:accessallgroups') if the
 * module uses that capability.
 *
 * @return array
 */
function annotation_get_extra_capabilities() {
    return array();
}

/* Gradebook API */

/**
 * Is a given scale used by the instance of annotation?
 *
 * This function returns if a scale is being used by one annotation
 * if it has support for grading and scales.
 *
 * @param int $annotationid ID of an instance of this module
 * @param int $scaleid ID of the scale
 * @return bool true if the scale is used by the given annotation instance
 */
function annotation_scale_used($annotationid, $scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('annotation', array('id' => $annotationid, 'grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if scale is being used by any instance of annotation.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param int $scaleid ID of the scale
 * @return boolean true if the scale is used by any annotation instance
 */
function annotation_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('annotation', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Creates or updates grade item for the given annotation instance
 *
 * Needed by {@link grade_update_mod_grades()}.
 *
 * @param stdClass $annotation instance object with extra cmidnumber and modname property
 * @param bool $reset reset grades in the gradebook
 * @return void
 */
function annotation_grade_item_update(stdClass $annotation, $reset=false) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    $item = array();
    $item['itemname'] = clean_param($annotation->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;

    if ($annotation->grade > 0) {
        $item['gradetype'] = GRADE_TYPE_VALUE;
        $item['grademax']  = $annotation->grade;
        $item['grademin']  = 0;
    } else if ($annotation->grade < 0) {
        $item['gradetype'] = GRADE_TYPE_SCALE;
        $item['scaleid']   = -$annotation->grade;
    } else {
        $item['gradetype'] = GRADE_TYPE_NONE;
    }

    if ($reset) {
        $item['reset'] = true;
    }

    grade_update('mod/annotation', $annotation->course, 'mod', 'annotation',
            $annotation->id, 0, null, $item);
}

/**
 * Delete grade item for given annotation instance
 *
 * @param stdClass $annotation instance object
 * @return grade_item
 */
function annotation_grade_item_delete($annotation) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    return grade_update('mod/annotation', $annotation->course, 'mod', 'annotation',
            $annotation->id, 0, null, array('deleted' => 1));
}

/**
 * Update annotation grades in the gradebook
 *
 * Needed by {@link grade_update_mod_grades()}.
 *
 * @param stdClass $annotation instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 */
function annotation_update_grades(stdClass $annotation, $userid = 0) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    // Populate array of grade objects indexed by userid.
    $grades = array();

    grade_update('mod/annotation', $annotation->course, 'mod', 'annotation', $annotation->id, 0, $grades);
}

/* File API */

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function annotation_get_file_areas($course, $cm, $context) {
    return array();
}

/**
 * File browsing support for annotation file areas
 *
 * @package mod_annotation
 * @category files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info instance or null if not found
 */
function annotation_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    return null;
}

/**
 * Serves the files from the annotation file areas
 *
 * @package mod_annotation
 * @category files
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the annotation's context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 */
function annotation_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options=array()) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);

    send_file_not_found();
}
