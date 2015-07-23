<?php
// This file is part of Moodle - http://moodle.org/
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
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_annotation
 * @copyright  2015 Jamie McGowan
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Replace annotation with the name of your module and remove this line.

global $CFG;
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

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

$event = \mod_annotation\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $annotation);
$event->trigger();

//Get the data about the file from the DB
$cmid = $cm->id;
$table = "annotation_document";
$results = $DB->get_records($table, array('cmid' => $cmid));
foreach ($results as $result) {
        $contenthash = $result->location;
        $document_type = $result->document_type;
        break;
}


if($document_type == 2) {
    //The document is an image
    //TODO render it, if have time
    die("Can't render images yet");
}

else if($document_type == 1) {
    //The docuemnt is a source code file
    $sourcecode = true;
    $PAGE->requires->css('/mod/annotation/scripts/styles/default.css');
    $PAGE->requires->js('/mod/annotation/scripts/highlight.pack.js');
    $PAGE->requires->js_init_call("hljs.initHighlightingOnLoad");
}


$PAGE->set_url('/mod/annotation/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($annotation->name));
$PAGE->set_heading(format_string($course->fullname));

/*
 * Other things you may want to set - remove if not needed.
 * $PAGE->set_cacheable(false);
 * $PAGE->set_focuscontrol('some-html-id');
 * $PAGE->add_body_class('annotation-'.$somevar);
 */

// Output starts here.
echo $OUTPUT->header();

// Conditions to show the intro can change to look for own settings or whatever.
if ($annotation->intro) {
    echo $OUTPUT->box(format_module_intro('annotation', $annotation, $cm->id), 'generalbox mod_introbox', 'annotationintro');
}

// Replace the following lines with you own code.
echo $OUTPUT->heading('Moodle Collaborative Annotation Plugin');


//--------TODO--------------


//Build the path to the file from the content_hash
$path = $CFG->dirroot.'\\..\\moodledata\\filedir\\';
$path = $path . substr($contenthash, 0, 2) . '\\' . substr($contenthash, 2, 2) . '\\';
$path = $path . $contenthash;
$file_contents = file_get_contents($path);

if($document_type == 1) {
    echo "<pre><code>";
}

$file_contents = htmlentities($file_contents); //always replace the HTML entities
echo $file_contents;

if($document_type == 1) {
    echo "</code></pre>";
}



//--------TODO--------------

// Finish the page.
echo $OUTPUT->footer();
