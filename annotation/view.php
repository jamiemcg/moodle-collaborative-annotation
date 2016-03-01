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
        $group_annotation = $result->group_annotation;
        $group_annotations_visible = $result->group_annotations_visible;
        $allow_from = $result->allow_from;
        $allow_until = $result->allow_until;
        break; //Bad way of doing this
}

$PAGE->requires->css('/mod/annotation/styles/main.css');

if($document_type == 2) {
    //The document_type is an image so load annotorious css/js
    $PAGE->requires->css('/mod/annotation/styles/annotorious.css');
    $PAGE->requires->js('/mod/annotation/scripts/annotorious.min.js');
    $PAGE->requires->js('/mod/annotation/scripts/annotorious-storage.js');
}
else {
    //The document is a plain text file (text document or source code)
    //Load annotator.js and custom storage plugin
    $PAGE->requires->css('/mod/annotation/styles/annotator.min.css');
    $PAGE->requires->js('/mod/annotation/scripts/annotator-full.min.js');
    $PAGE->requires->js('/mod/annotation/scripts/annotator-storage.js');
}

if($document_type == 1) {
    //The docuement is a source code file so load highlight.js css/js
    $sourcecode = true;
    $PAGE->requires->css('/mod/annotation/styles/highlight.css');
    $PAGE->requires->js('/mod/annotation/scripts/highlight.pack.js');
    $PAGE->requires->js_init_call("hljs.initHighlightingOnLoad");
}

//Load main.js last to ensure the page has initialized
$PAGE->requires->js('/mod/annotation/scripts/main.js');

$PAGE->set_url('/mod/annotation/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($annotation->name));
$PAGE->set_heading(format_string($course->fullname));

// Output starts here.
echo $OUTPUT->header();

echo $OUTPUT->heading($annotation->name);

// If an intro (description) exists for the current activity, display it
if ($annotation->intro) {
    echo $OUTPUT->box(format_module_intro('annotation', $annotation, $cm->id), 'generalbox mod_introbox', 'annotationintro');
    echo "<hr>";
}

//Determine if user is student or teacher
$context = context_course::instance($course->id);
$teacher = has_capability('mod/annotation:manage', $context);

//If availability settings are defined, display the settings
if($allow_from && $allow_until) {
    echo get_string('annotatable_from', 'annotation') . " " . date('d/m/Y H:i:s', $allow_from) . " " . get_string('until', 'annotation') . " " . date('d/m/Y H:i:s', $allow_until);
    echo "<br>";
}
else if($allow_from) {
    echo get_string('annotatable_from', 'annotation') . " " . date('d/m/Y H:i:s', $allow_from);
    echo "<br>";
}
else if($allow_until) {
    echo get_string('annotatable_until', 'annotation') . " " . date('d/m/Y H:i:s', $allow_until);
    echo "<br>";
}
else {
    //Not set, do nothing
}

// If groups are enabled and the current user is a teacher or group visibility is enabled
// display the names of the groups so they are available for filtering

if($group_annotation && ($group_annotations_visible || $teacher)) {
    //Find and display all of the group names relevant to this activity (i.e. the course groups)    
    $params = array(
                    "courseid" => $course->id
                   );

    $table ="groups";
    $count = $DB->count_records($table, $params);
    //Only print "Groups:" if any groups actually exist
    if($count > 0) {
        echo "<p>Groups:</p>";
        $sql = "SELECT * FROM mdl_groups WHERE courseid = ?";
        $rs = $DB->get_recordset_sql($sql, array($course->id));

        echo "<ul class='group-list'>";
        foreach ($rs as $group) {
            echo "<li>" . $group->name . "</li>";
        }
        echo "</ul>";
    }
}

else if($group_annotation) {
    //Group annotation is enabled but not group visibility, do not display group names, only show
    //annotations created by this group
}
else {
    //No groups
}


//Build the path to the file from the content_hash
$path = $CFG->dataroot . DIRECTORY_SEPARATOR . "filedir" . DIRECTORY_SEPARATOR;
$path = $path . substr($contenthash, 0, 2) . DIRECTORY_SEPARATOR  . substr($contenthash, 2, 2) . DIRECTORY_SEPARATOR ;
$path = $path . $contenthash;
$file_contents = file_get_contents($path);

//Check if it is an image
if($document_type == 2) {
    //Can't render the images directly, have to determine MIME type and base64 encode
    //Need to find out MIME type from mdl_files table
    $table = "files";
    $results = $DB->get_records($table, array('contenthash' => $contenthash));
    foreach ($results as $result) {
            $mimetype = $result->mimetype;
            break;
    }

    $base64 = base64_encode($file_contents);
    echo '<img class="annotatable" data-original="http://image.to.annotate" src="data:' . $mimetype . ';base64,' . $base64 . '">';
}
else {
    //It is a plain text document 
	echo '<div id="annotator-content">'; //Start of annotatable content

    if($document_type == 1) {
        //It is source code
        echo "<pre><code>";
    }
    else {
        echo "<pre class='pre-text'>";
    }

    $file_contents = htmlentities($file_contents); //always replace the HTML entities
    echo $file_contents;

    if($document_type == 1) {
        echo "</code></pre>";
    }
    else {
        echo "</pre>";
    }

    echo '</div>'; //The end of annotatable content
}
?>

<!-- Add the side bar to the page, will be populated client side by JavaScript -->
<nav class="nav-side">
    <div class="annotation-list" id="annotation-list">
        <h2>Annotations</h2>
    </div>
    <a href="#" class="nav-toggle"></a>
</nav>

<?php
echo $OUTPUT->footer();
