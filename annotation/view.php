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

// Get the data about the file from the DB.
$cmid = $cm->id;
$table = "annotation_document";
$results = $DB->get_records($table, array('cmid' => $cmid));
foreach ($results as $result) {
        $contenthash = $result->location;
        $documenttype = $result->documenttype;
        $groupannotation = $result->groupannotation;
        $groupannotationsvisible = $result->groupannotationsvisible;
        $allowfrom = $result->allowfrom;
        $allowuntil = $result->allowuntil;
        break; // Bad way of doing this.
}

$PAGE->requires->css('/mod/annotation/styles/main.css');

if ($documenttype == 2) {
    // The documenttype is an image so load annotorious css/js.
    $PAGE->requires->css('/mod/annotation/styles/annotorious.css');
    $PAGE->requires->js('/mod/annotation/scripts/annotorious.min.js');
    $PAGE->requires->js('/mod/annotation/scripts/annotorious-storage.js');
} else {
    // The document is a plain text file (text document or source code).
    // Load annotator.js and custom storage plugin.
    $PAGE->requires->css('/mod/annotation/styles/annotator.min.css');
    $PAGE->requires->js('/mod/annotation/scripts/annotator-full.min.js');
    $PAGE->requires->js('/mod/annotation/scripts/annotator-storage.js');
    $PAGE->requires->css('/mod/annotation/styles/annotator.touch.css');
    $PAGE->requires->js('/mod/annotation/scripts/annotator.touch.js');
}

if ($documenttype == 1) {
    // The docuement is a source code file so load highlight.js css/js.
    $sourcecode = true;
    $PAGE->requires->css('/mod/annotation/styles/highlight.css');
    $PAGE->requires->js('/mod/annotation/scripts/highlight.pack.js');
    $PAGE->requires->js_init_call("hljs.initHighlightingOnLoad");
}

// Load main.js last to ensure the page has initialized.
$PAGE->requires->js('/mod/annotation/scripts/main.js');

$PAGE->set_url('/mod/annotation/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($annotation->name));
$PAGE->set_heading(format_string($course->fullname));

// Determine if user is student or teacher.
$context = context_course::instance($course->id);
$teacher = has_capability('mod/annotation:manage', $context);

// Output starts here.
echo $OUTPUT->header();

echo $OUTPUT->heading($annotation->name);

// If an intro (description) exists for the current activity, display it.
if ($annotation->intro) {
    echo $OUTPUT->box(format_module_intro('annotation', $annotation, $cm->id), 'generalbox mod_introbox', 'annotationintro');
}

// Add a button to allow users to switch to the 'discussion view' page.
echo "<a href='view_discussion.php?id=$cmid&type=$documenttype'>";
echo "<button>" . get_string('discussion_view', 'annotation') . "</button>";
echo "</a>";

// If a teacher/admin/manager is logged in, add button for exporting annotation data.
if ($teacher) {
    $filetitle = format_string($annotation->name);
    echo "<a href='export.php?url=$cmid&type=$documenttype'>";
    echo "<button>" .  get_string('export_data', 'annotation') . "</button>";
    echo "</a>";
}


echo "<hr>";

// If availability settings are defined, display the settings.
if ($allowfrom && $allowuntil) {
    echo get_string('annotatable_from', 'annotation') . " " . date('d/m/Y H:i:s', $allowfrom) . " " .
                            get_string('until', 'annotation') . " " . date('d/m/Y H:i:s', $allowuntil);
    echo "<br>";
} else if ($allowfrom) {
    echo get_string('annotatable_from', 'annotation') . " " . date('d/m/Y H:i:s', $allowfrom);
    echo "<br>";
} else if ($allowuntil) {
    echo get_string('annotatable_until', 'annotation') . " " . date('d/m/Y H:i:s', $allowuntil);
    echo "<br>";
}

// If groups are enabled and the current user is a teacher or group visibility is enabled,
// Display the names of the groups so they are available for filtering.

if ($groupannotation && ($groupannotationsvisible || $teacher)) {
    // Find and display all of the group names relevant to this activity (i.e. the course groups).
    $params = array(
                    "courseid" => $course->id
                   );

    $table = "groups";
    $count = $DB->count_records($table, $params);
    // Only print "Groups:" if any groups actually exist.
    if ($count > 0) {
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


// Build the path to the file from the content_hash.
$path = $CFG->dataroot . DIRECTORY_SEPARATOR . "filedir" . DIRECTORY_SEPARATOR;
$path = $path . substr($contenthash, 0, 2) . DIRECTORY_SEPARATOR  . substr($contenthash, 2, 2) . DIRECTORY_SEPARATOR;
$path = $path . $contenthash;
$filecontents = file_get_contents($path);

// Check if it is an image.
if ($documenttype == 2) {
    // Can't render images directly have to determine MIME type and base64 encode.
    // Need to find out MIME type from mdl_files table.
    $table = "files";
    $results = $DB->get_records($table, array('contenthash' => $contenthash));
    foreach ($results as $result) {
            $mimetype = $result->mimetype;
            break;
    }

?>
    <!-- Add custom filter bar above the displayed image (on image annotation only) -->
    <div class="filter-bar">
        <span class="filter-item">
        <label class="filter-label" for="annotation">Annotation:</label>
        <input class="filter-input" type="search" name="annotation" id="filter-annotation" placeholder="Filter by Annotation...">
        </span>
        <span class="filter-item">
            <label class="filter-label" for="group">Group:</label>
            <input class="filter-input" type="search" name="group" id="filter-group" placeholder="Filter by Group...">
        </span>
        <span class="filter-item">
            <label class="filter-label" for="tag">Tag:</label>
            <input class="filter-input" type="search" name="tag" id="filter-tag" placeholder="Filter by Tag...">
        </span>
        <span class="filter-item">
            <label class="filter-label" for="user">User:</label>
            <input class="filter-input" type="search" name="user" id="filter-user" placeholder="Filter by User...">
        </span>
    </div>
    <!-- TODO GET STRING for Clear Filters -->
    <button onclick="clearFilter()"><?php echo get_string('clear_filters', 'annotation'); ?></button>
    <br>

<?php


    $base64 = base64_encode($filecontents);
    echo '<img class="annotatable" data-original="http://image.to.annotate" src="data:' . $mimetype . ';base64,' . $base64 . '">';
} else {
    // It is a plain text document.
    echo '<div id="annotator-content">'; // Start of annotatable content.

    if ($documenttype == 1) {
        // It is source code.
        echo "<pre><code>";
    } else {
        echo "<pre class='pre-text'>";
    }

    $filecontents = htmlentities($filecontents); // Always replace the HTML entities.
    echo $filecontents;

    if ($documenttype == 1) {
        echo "</code></pre>";
    } else {
        echo "</pre>";
    }

    echo '</div>'; // The end of annotatable content.
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
