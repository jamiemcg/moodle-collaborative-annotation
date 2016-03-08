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
 * English strings for annotation
 *
 * @package    mod_annotation
 * @copyright  2015 Jamie McGowan
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

//Standard/required moodle strings
$string['annotation'] = 'annotation';
$string['annotationname'] = 'Activity Name';
$string['annotationname_help'] = 'This is the name that users will see for the activity.';
$string['modulename'] = 'Annotated Document';
$string['modulename_help'] = 'The annotation activity module allows users to collaboratively annotate documents. Accepted formats are Plain Text Files, Source Code and Images.

Teachers can manage the visibility of annotations by enabling groups support, allowing the students to annotate files in groups for peer learning.

Teachers can restrict the time that a file is annotatable, making the module useful for in-class learning. Teachers can also choose to grade students for their annotations.
';
$string['modulenameplural'] = 'Annotated Documents';
$string['pluginadministration'] = 'Annotation administration';
$string['pluginname'] = 'annotation';

//Customs strings for annotation
$string['allow_annotations_from'] = 'Allow annotations from';
$string['allow_annotations_from_help'] = 'If enabled, students will be able to view but not annotate the file before this date. If disabled, students will be able to start annotating straight away.';
$string['allow_annotations_until'] = 'Allow annotations until';
$string['allow_annotations_until_help'] = 'If enabled, students will not be able to annotate the file after this date. They will still be able to view the file and any existing annotations. If disabled, students will always be able to view and annotate the file.';
$string['allow_comments'] = 'Allow comments';
$string['allow_comments_help'] = 'If enabled, students will be able to comment on annotations.';
$string['annotation_availability'] = 'Availability';
$string['content'] = 'Content';
$string['document_type'] = 'Document Type';
$string['document_type_help'] = 'Select the correct document type to ensure correct rendering.';
$string['export_data'] = 'Export annotation data';
$string['file_select_info'] = 'Please choose either a plain text file or an image and specify the correct document type';
$string['filemanager'] = '';
$string['filemanager_help'] = 'Note: You can not change the selected file once you save the activity. Choose either a plain text file or an image.';
$string['group_annotation'] = 'Group Annotation';
$string['group_annotations'] = 'Students annotate in groups';
$string['group_annotations_help'] = 'If enabled, students will be divided into groups based on the default group settings for this course. Users will then be able to filter annotations by groups.';
$string['group_annotations_visible'] = 'Students can view annotations from other groups';
$string['group_annotations_visible_help'] = 'If enabled, students will be able to view annotations created by other groups that they do not belong to. Teachers will be able to view annotations from all groups.';
$string['image'] = 'Image';
$string['selectfile'] = 'Select File';
$string['source_code'] = 'Source Code';
$string['teacher_permissions'] = 'Allow teachers to edit and delete students annotations';
$string['text_document'] = 'Text Document';

//Strings for view.php
$string['annotatable_from'] = 'This file is annotatable from';
$string['annotatable_until'] = 'This file is annotatable until';
$string['until'] = 'until';