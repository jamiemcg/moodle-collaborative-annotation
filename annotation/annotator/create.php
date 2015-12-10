<?php

/**
 * Stores an annotation with the data received from a POST request
 * Must attatch timestamp, id, user_id to the annotation
 * Returns the id of the created annotation
 */

if(!empty($_POST)) {
	require_once(__DIR__ . "../../../../config.php");
	$cmid = $_POST['url'];
	require_once("../initialize.php");
	
	
	require_login();

	global $CFG, $DB, $USER;

	if(strlen($_POST['text']) < 1) {
		//No text selected, stop script and don't store annotation
		//Should be blocked client side anyway
		die();
	}

	$annotation = new stdClass();
	$annotation->url = $_POST['url'];
	$annotation->ranges = json_encode($_POST['ranges']);
	$annotation->quote = htmlentities($_POST['quote']);
	$annotation->highlights = json_encode($_POST['highlights']);
	$annotation->annotation = htmlentities($_POST['text']);
	
	if(isset($_POST['tags']) && !empty($_POST['tags'])) {
		$annotation->tags = json_encode($_POST['tags']);
	}
	
	$annotation->timecreated = time();
	$annotation->id = 1; //DB will change this
	$annotation->userid = $USER->id;
	
	$annotation->group_id = $group;

	$table = "annotation_annotation";
	$lastinsertid = $DB->insert_record($table, $annotation);
	$annotation->id = $lastinsertid;
	$annotation->username = $USER->firstname . " " . $USER->lastname;

	if($group_annotation) {
		//Gets the name of the group that the current user belongs to
		$annotation->groupname = groups_get_group_name($group);
	}
	
	echo json_encode($annotation);
}
else {
	http_response_code(400);
}
