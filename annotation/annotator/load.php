<?php

/**
 * Loads the annotations from the server for the image currently being viewed.
 * POST request with the window.location.url (cmid)
 */
if(!empty($_POST['url'])) {
	require_once(__DIR__ . "../../../../config.php");
	require_once("$CFG->dirroot/mod/annotation/locallib.php");
	require_login();

	global $CFG, $DB, $USER;

	$userid = $USER->id; //Gets the current users id
	$url = $_POST['url'];
	$cmid = $url; //Used by initialize.php
	require_once("../initialize.php");

	$cm = get_coursemodule_from_id('annotation', $url, 0, false, MUST_EXIST);

	//Determine if user is student or teacher
	$context = context_course::instance($cm->course);
	$teacher = has_capability('mod/annotation:manage', $context);

	if($group_annotation && $teacher) {
		//The user is a teacher, load all annotations [from every group]
		$sql = "SELECT * FROM mdl_annotation_annotation WHERE url = ?";
		$rs = $DB->get_recordset_sql($sql, array($url));
	}
	else if($group_annotation && ! $group_annotations_visible) {
		//Load only annotations for this group
		$sql = "SELECT * FROM mdl_annotation_annotation WHERE url = ? AND group_id = ?" ;
		$rs = $DB->get_recordset_sql($sql, array($url, $group));
	}
	else {
		$sql = "SELECT * FROM mdl_annotation_annotation WHERE url = ?";
		$rs = $DB->get_recordset_sql($sql, array($url));
	}

	$response = array();

	$response[] = $editable; //Testing block annotations if they are disabled; Could be interrupted?

	//Loop through results
	foreach($rs as $record) {
		//Get username of annotation creator
		$user = $DB->get_record('user', array("id" =>$record->userid));
		$record->username = $user->firstname . " " . $user->lastname;
	
		//Determine group name from the annotations group id if groups enabled
		if($group_annotation) {
				$record->groupname = groups_get_group_name($record->group_id);
		}
		
		//Don't need to return the user's id or the url
		unset($record->userid); 
		unset($record->url);
		
		if($record->tags == "") {
			$record->tags = null;
		}

		$response[] = $record;
	}
	$rs->close(); //Close the record set
	echo json_encode($response);
}
else {
	http_response_code(400);
}
