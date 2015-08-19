
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

	//Create a new object to store the annotation data
	$annotation = new stdClass();
	$annotation->id = 0; //This will be changed by the DB
	$annotation->userid = $USER->id;
	$annotation->annotation = htmlentities($_POST['text']);
	$annotation->shapes = json_encode($_POST['shapes']);
	$annotation->url = $_POST['url'];
	$annotation->timecreated = time();
	$annotation->tags = htmlentities(json_decode($_POST['tags'])); //TODO
	$annotation->group_id = $group;

	$table = "annotation_image";

	//Insert into DB and get the id

	$lastinsertid = $DB->insert_record($table, $annotation);
	$annotation->id = $lastinsertid;
	$annotation->username = $USER->firstname . " " . $USER->lastname;
	$annotation->groupname = groups_get_group_name($group);
	//Returns the data to the client for processing
	
	//TODO waste of data transfer, only return what is
	//required: id, username, timecreated....
	echo json_encode($annotation);
}
else {
	http_response_code(400);
}
