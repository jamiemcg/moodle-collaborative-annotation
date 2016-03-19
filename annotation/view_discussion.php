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
 * Prints a particular instance of annotation
 *
 * @package    mod_annotation
 * @copyright  2015 Jamie McGowan
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once("$CFG->dirroot/mod/annotation/locallib.php");


$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // ... annotation instance ID - it should be named as the first character of the module.

if ($id) {
    $cm         = get_coursemodule_from_id('annotation', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $annotation  = $DB->get_record('annotation', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $annotation  = $DB->get_record('annotation', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $annotation->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('annotation', $annotation->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$cmid = $cm->id;

//Determine if user is student or teacher
$context = context_course::instance($course->id);
$teacher = has_capability('mod/annotation:manage', $context);

$PAGE->requires->css('/mod/annotation/styles/main.css');
$PAGE->requires->js('/mod/annotation/scripts/main.js');
$PAGE->requires->js('/mod/annotation/scripts/discussion.js'); // JS specific to this page

$document_type = $_GET['type'];

if($document_type == 1) {
    $PAGE->requires->css('/mod/annotation/styles/highlight.css');
    $PAGE->requires->js('/mod/annotation/scripts/highlight.pack.js');
}

$PAGE->set_url('/mod/annotation/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($annotation->name . " | Discussion View"));
$PAGE->set_heading(format_string($course->fullname));

// Output starts here.
echo $OUTPUT->header();

echo $OUTPUT->heading($annotation->name . " | Discussion View");

// If an intro (description) exists for the current activity, display it
if ($annotation->intro) {
    echo $OUTPUT->box(format_module_intro('annotation', $annotation, $cm->id), 'generalbox mod_introbox', 'annotationintro');
}

// Add a button to allow users to switch back to the 'annotation view' page
echo "<a href='view.php?id=$cmid'>";
echo "<button>" . get_string('annotation_view', 'annotation') . "</button>";
echo "</a>";

// If a teacher/admin/manager is logged in, add button for exporting annotation data
if ($teacher) {
    echo "<a href='export.php?url=$cmid&type=$document_type'>";
    echo "<button>" .  get_string('export_data', 'annotation') . "</button>";
    echo "</a>";
}

echo "<hr>";

echo "<div id='discussion-area'></div>"; // Populated with annotations by JS


// End of custom output, add default footer, blocks, etc
echo $OUTPUT->footer();