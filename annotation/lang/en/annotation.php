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
 * English strings for annotation
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_annotation
 * @copyright  2015 Jamie McGowan
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

//Standard/required moodle strings
$string['annotation'] = 'annotation';
$string['annotationname'] = 'Document Name';
$string['annotationname_help'] = 'This is the name of the document/file that users will see for the activity.';
$string['modulename'] = 'Annotated Document';
$string['modulename_help'] = 'The annotation module allows users to collaboratively annotate documents. Accepted formats are Plain Text Documents, Source Code and Images.'; //TODO extend this string
$string['modulenameplural'] = 'Annotated Documents';
$string['pluginadministration'] = 'Annotation administration';
$string['pluginname'] = 'annotation';

//Customs strings for annotation
$string['allow_annotations_from'] = 'Allow annotations from';
$string['allow_annotations_from_help'] = 'If enabled, students will be able to view but not annotate the file before this date. If disabled, students will be able to start annotating straight away.';
$string['allow_annotations_until'] = 'Allow annotations until';
$string['allow_annotations_until_help'] = 'If enabled, students will not be able to annotate the file after this date. They will still be able to view the file and any existing annotations. If disabled, students will always be able to view and annotate the file.';
$string['annotation_availability'] = 'Availability';
$string['content'] = 'Content';
$string['document_type'] = 'Document Type';
$string['document_type_help'] = '';
$string['file_select_info'] = 'Please choose either a plain text file or an image and specify the correct document type';
$string['group_annotation'] = 'Group Annotation';
$string['group_annotations'] = 'Students annotate in groups';
$string['group_annotations_help'] = 'If enabled, students will be divided into groups based on the default set of groups for the course. Users will then be able to filter annotations by groups.';
$string['group_annotations_visible'] = 'Students can view annotations from other groups';
$string['group_annotations_visible_help'] = 'If enabled, students will be able to view annotations created by other groups that they do not belong to.';
$string['image'] = 'Image';
$string['selectfile'] = 'Select File';
$string['source_code'] = 'Source Code';
$string['teacher_permissions'] = 'Allow teachers to edit and delete students annotations';
$string['text_document'] = 'Text Document';

//Strings for view.php
$string['annotatable_from'] = 'This file is annotatable from';
$string['annotatable_until'] = 'This file is annotatable until';
$string['until'] = 'until';