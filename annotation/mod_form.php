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
 * The main annotation configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_annotation
 * @copyright  2015 Jamie McGowan
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once ($CFG->libdir.'/formslib.php');

/**
 * Module instance settings form
 *
 * @package    mod_annotation
 * @copyright  2015 Jamie McGowan
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_annotation_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('annotationname', 'annotation'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'annotationname', 'annotation');

        // Adding the standard "intro" and "introformat" fields.
        $this->standard_intro_elements();

        // Add a file manager to handle uploading of files.
        $mform->addElement('header', 'contentsection', get_string('contentheader', 'resource'));
        $mform->setExpanded('contentsection');
        $mform->addElement('html', '<b>' . get_string('file_select_info', 'annotation') . '</b><br>');

        //Add selector for document type - text, source, image
        $doctypes = array(0 => get_string('text_document', 'annotation'), 1 => get_string('source_code', 'annotation'), 2 => get_string('image', 'annotation'));
        $mform->addElement('select', 'type', get_String('document_type', 'annotation'), $doctypes);
        $mform->addRule('type', null, 'required', null, 'client');

        $filemanager_options = array();
        $filemanager_options['accepted_types'] = '*';
        $filemanager_options['maxbytes'] = 0;
        $filemanager_options['maxfiles'] = 1;
        $filemanager_options['mainfile'] = true;
        $mform->addElement('filemanager', 'files', get_string('selectfile', 'annotation'), null, $filemanager_options);
        $mform->addRule('files', null, 'required', 'client');

        //Group options settings
        $mform->addElement('header', 'group', get_string('group_annotation', 'annotation'));
        $mform->addElement('html', '<mark>Not yet implemented</mark><br>');
        
        $name = get_string('group_annotations', 'annotation');
        $mform->addElement('selectyesno', 'group_annotation', $name);
        $mform->addHelpButton('group_annotation', 'group_annotations', 'annotation');

        $name = get_string('group_annotations_visible', 'annotation');
        $mform->addElement('selectyesno', 'group_annotations_visible', $name);
        $mform->addHelpButton('group_annotations_visible', 'group_annotations_visible', 'annotation');

        //Availabilty / time restriction section
        $mform->addElement('header', 'availability', get_string('annotation_availability', 'annotation'));
        $mform->setExpanded('availability', false);
        $mform->addElement('html', '<mark>Not yet implemented</mark><br>');

        $name = get_string('allow_annotations_from', 'annotation');
        $mform->addElement('date_time_selector', 'allow_from', $name, array('optional'=>true));
        $mform->addHelpButton('allow_from', 'allow_annotations_from', 'annotation');

        $name = get_string('allow_annotations_until', 'annotation');
        $mform->addElement('date_time_selector', 'allow_until', $name, array('optional'=>true));
        $mform->addHelpButton('allow_until', 'allow_annotations_until', 'annotation');

        // Add standard elements, common to all modules
        $this->standard_coursemodule_elements();

        // Add standard grading elements
        $this->standard_grading_coursemodule_elements();

        // Add standard buttons, common to all modules
        $this->add_action_buttons();
	}


    public function definition_after_data() {
    	parent::definition_after_data();
        global $DB;
    	$mform = $this->_form;
    	$cmid = $mform->getElementValue('coursemodule');
    	if(!empty($cmid)) {
    		//The user is updating the instance of the activity
            //Remove the file manager as the annotations may not make 
            //sense if the file they are applied to are changed
            $mform->removeElement('files');

            //TODO: remove group settings for editing??

            //Find out what type of document it was and set selector
            $table = "annotation_document";
            $results = $DB->get_records($table, array('cmid' => $cmid));
            foreach ($results as $result) {
                    $document_type = $result->document_type;
                    $group_annotation = $result->group_annotation;
                    $group_annotations_visible = $result->group_annotations_visible;
                    $allow_from = $result->allow_from;
                    $allow_until = $result->allow_until;
                    break;
            }
            $mform->setDefault('type', $document_type);
            $mform->setDefault('group_annotation', $group_annotation);
            $mform->setDefault('group_annotations_visible', $group_annotations_visible);
            $mform->setDefault('allow_from', $allow_from);
            $mform->setDefault('allow_until', $allow_until);
    	}
    }
}
