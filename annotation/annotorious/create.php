<?php

/**
 * Stores an annotation with the data received from a POST request
 * Must attatch timestamp, id, user_id to the annotation
 * Returns the id of the created annotation
 * 
 */

if(!empty($_POST)) {
	require_once(__DIR__ . "../../../../config.php");
	require_login();


	global $CFG, $DB, $USER;

	//Create a new annotation object
	//Populate it with *some* of the information from the POST request
	//Add other data -> timestamp, user, etc...
	//Store it in the DB
	//Return the ID to the client
	//Return the timecreated and user to the client for display

	$annotation = new stdClass();
	$annotation->id = 0; //This will be changed by the DB
	$annotation->userid = $USER->id;
	$annotation->username = $USER->firstname . " " . $USER->lastname;
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
	echo json_encode($annotation);
}
