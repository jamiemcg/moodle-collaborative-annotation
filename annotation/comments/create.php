<?php

/**
 * !! check if commenting enabled !!
 * don't return comment, waste of data transfer
 */

if(!empty($_POST)) {
	require_once(__DIR__ . "../../../../config.php");
	$cmid = $_POST['url'];
	require_once("../initialize.php");
	require_login();

	global $CFG, $DB, $USER;

	if(strlen($_POST['comment']) < 1) {
		//No text selected, do not store annotation
		//Also blocked client side so should never run
		die();
	}

	$comment = new stdClass();
	$comment->url = $_POST['url'];
	$comment->annotation_id = $_POST['annotation_id'];
	$comment->timecreated = time();
	$comment->comment = htmlentities($_POST['comment']);
	$comment->user_id = $USER->id;
	$comment->id = 0; //This will be changed by the DB

	$table = "annotation_comment"; //the table name
	//Insert into DB and get the id
	$lastinsertid = $DB->insert_record($table, $comment); //This causes 404, 'user_id' no default?

	//Send the user name and timecreated back to the client
	//Also return comment's id for completeness (may be used in future)
	$response = new stdClass();
	$response->username = $USER->firstname . " " . $USER->lastname;
	$response->timecreated = $comment->timecreated;
	$response->id = $lastinsertid;
	echo json_encode($response);
}
else {
	http_response_code(400);
}
