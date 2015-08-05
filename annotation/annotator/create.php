<?php

/**
 * Stores an annotation with the data received from a POST request
 * Must attatch timestamp, id, user_id to the annotation
 * Returns the id of the created annotation
 */

if(!empty($_POST)) {
	require_once(__DIR__ . "../../../../config.php");
	require_login();

	global $CFG, $DB, $USER;

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

	$table = "annotation_annotation";
	$lastinsertid = $DB->insert_record($table, $annotation);
	$annotation->id = $lastinsertid;
	$annotation->username = $USER->firstname . " " . $USER->lastname;
	
	echo json_encode($annotation);
}
else {
	http_response_code(400);
}
