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
require_once($CFG->libdir.'/formslib.php');

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

        // Add selector for document type - text, source, image.
        $doctypes = array(0 => get_string('text_document', 'annotation'), 1 => get_string('source_code', 'annotation'),
            2 => get_string('image', 'annotation'));
        $mform->addElement('select', 'type', get_string('documenttype', 'annotation'), $doctypes);
        $mform->addRule('type', null, 'required', null, 'client');
        $mform->addHelpButton('type', 'documenttype', 'annotation');

        $filemanageroptions = array();
        $filemanageroptions['accepted_types'] = '*';
        $filemanageroptions['maxbytes'] = 0;
        $filemanageroptions['maxfiles'] = 1;
        $filemanageroptions['mainfile'] = true;
        $mform->addElement('filemanager', 'files', get_string('selectfile', 'annotation'), null, $filemanageroptions);
        $mform->addRule('files', null, 'required', 'client');
        $mform->addHelpButton('files', 'filemanager', 'annotation');

        // Group options settings.
        $mform->addElement('header', 'group', get_string('groupannotation', 'annotation'));
        $mform->addElement('html', '<p>' . get_string('groupannotation_tip', 'annotation') . '</p>');

        $name = get_string('groupannotations', 'annotation');
        $mform->addElement('selectyesno', 'groupannotation', $name);
        $mform->addHelpButton('groupannotation', 'groupannotations', 'annotation');

        $name = get_string('groupannotationsvisible', 'annotation');
        $mform->addElement('selectyesno', 'groupannotationsvisible', $name);
        $mform->addHelpButton('groupannotationsvisible', 'groupannotationsvisible', 'annotation');
        $mform->disabledIf('groupannotationsvisible', 'groupannotation', 'eq', 0);

        // Availabilty / time restriction section.
        $mform->addElement('header', 'availability', get_string('annotation_availability', 'annotation'));
        $mform->setExpanded('availability', false);

        $name = get_string('allow_annotations_from', 'annotation');
        $mform->addElement('date_time_selector', 'allowfrom', $name, array('optional' => true));
        $mform->addHelpButton('allowfrom', 'allow_annotations_from', 'annotation');

        $name = get_string('allow_annotations_until', 'annotation');
        $mform->addElement('date_time_selector', 'allowuntil', $name, array('optional' => true));
        $mform->addHelpButton('allowuntil', 'allow_annotations_until', 'annotation');

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard grading elements.
        $this->standard_grading_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }


    public function definition_after_data() {
        parent::definition_after_data();
        global $DB;
        $mform = $this->_form;
        $cmid = $mform->getElementValue('coursemodule');

        $mform->removeElement('groupmode'); // Remove group mode as cutom method is implemented.

        if (!empty($cmid)) {
            // The user is updating the instance of the activity.
            // Remove the file manager as the annotations may not make .
            // sense if the file they are applied to are changed.
            $mform->removeElement('files');

            // Find out what type of document it was and set selector.
            $table = "annotation_document";
            $results = $DB->get_records($table, array('cmid' => $cmid));
            foreach ($results as $result) {
                    $documenttype = $result->documenttype;
                    $groupannotation = $result->groupannotation;
                    $groupannotationsvisible = $result->groupannotationsvisible;
                    $allowfrom = $result->allowfrom;
                    $allowuntil = $result->allowuntil;
                    break;
            }

            $mform->setDefault('type', $documenttype);
            $mform->setDefault('groupannotation', $groupannotation);
            $mform->setDefault('groupannotationsvisible', $groupannotationsvisible);
            $mform->setDefault('allowfrom', $allowfrom);
            $mform->setDefault('allowuntil', $allowuntil);
        }
    }
}
