<?php

/**
 * Loads comments from the server for the annotation_activity
 * Responds to a POST request with the url -> cmid
 */

if(!empty($_POST['url'])) {
	require_once(__DIR__ . "../../../../config.php");
	require_once("$CFG->dirroot/mod/annotation/locallib.php");
	require_login();

	global $CFG, $DB, $USER;

	$user_id = $USER->id; //Gets the current users id
	$url = $_POST['url'];
	
	//Select all comments where the url matches that posted
	$sql = "SELECT * FROM mdl_annotation_comment WHERE url = ?";
	$rs = $DB->get_recordset_sql($sql, array($url));

	$response = array(); //Store the response in an array

	foreach($rs as $record) {
		$user = $DB->get_record('user', array("id" => $record->user_id));
		$record->username = $user->firstname . " " . $user->lastname;

		unset($record->user_id); //Don't send the user's id to the client

		$response[] = $record; //Apend the record to the response array
	}
	
	$rs->close(); //Close the record set
	echo json_encode($response); //Return the comments
}
else {
	http_response_code(400);
}
