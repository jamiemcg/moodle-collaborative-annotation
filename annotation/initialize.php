<?php

/**
  * Determines the current users group
  * Determines the availability timing settings
  */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

require_login();

global $CFG, $DB, $USER;
$cm = get_coursemodule_from_id('annotation', $cmid, 0, false, MUST_EXIST);

//Determine group settings

$table = "annotation_document";
$results = $DB->get_records($table, array('cmid' => $cmid));
foreach ($results as $result) {
        $group_annotation = $result->group_annotation;
        $group_annotations_visible = $result->group_annotations_visible;
        $allow_from = $result->allow_from;
        $allow_until = $result->allow_until;
        break; //Bad way of doing this
}

//Determine the user's group even if group mode is disabled in case teacher 
//changes settings in the future
$group = groups_get_user_groups($cm->course, $USER->id);
if(count($group[0]) > 0) { //User may not have a group? E.g teacher?
	$group = $group[0][0];
}
else {
	$group = -1; //Set to -1 if teacher, or if group undefined
}

//Determine availability settings
$editable = check_time_constraint($allow_from, $allow_until);
