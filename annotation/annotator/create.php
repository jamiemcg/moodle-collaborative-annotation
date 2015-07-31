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

	//Create a new object to for storing the annotation
	$annotation = new stdClass();
	$annotation->id = 0; //DB will change this later
	$annotation->userid = $USER->id;
	$annotation->annotation = htmlentities($_POST['text']);
	$annotation->ranges = json_encode($_POST['ranges']);
	$annotation->tags = json_encode(htmlentities($_POST['tags']));
	$annotation->highlights = json_encode($_POST['highlights']);
	$annotation->quote = htmlentities($_POST['quote']);
	$annotation->timecreated = time();

	$table = "annotation_annotation";

	//Insert the annotation into the DB and get its id
	$lastinsertid = $DB->insert_record($table, $annotation);
	$annotation->id = $lastinsertid;
	$annotation->username = $USER->firstname . " " . $USER->lastname;

	//TODO send less stuff back, waste of transfer
	echo json_encode($annotation);
}
