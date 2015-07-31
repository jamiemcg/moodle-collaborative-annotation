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

	//Create a new object to store the annotation data
	$annotation = new stdClass();
	$annotation->id = 0; //This will be changed by the DB
	$annotation->userid = $USER->id;
	$annotation->username = $USER->firstname . " " . $USER->lastname; //TODO rewrite this, don't store, get username dynamically incase of changes
	$annotation->annotation = htmlentities($_POST['text']);
	$annotation->shapes = json_encode($_POST['shapes']);
	$annotation->url = $_POST['url'];
	$annotation->timecreated = time();
	$annotation->tags = htmlentities(json_decode($_POST['tags'])); //TODO

	$table = "annotation_image";

	//Insert into DB and get the id
	$lastinsertid = $DB->insert_record($table, $annotation);
	$annotation->id = $lastinsertid;

	//Returns the data to the client for processing
	//TODO waste of data transfer, only return what it
	//required: id, username, timecreated....
	echo json_encode($annotation);
}
