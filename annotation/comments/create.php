<?php


/*
	check if commenting enabled
	don't return comment, waste of data transfer

*/

if(!empty($_POST)) {
	require_once(__DIR__ . "../../../../config.php");
	$cmid = $_POST['url'];
	require_once("../initialize.php");
	require_login();

	global $CFG, $DB, $USER;

	if(strlen($_POST['comment']) < 1) {
		//No text selected, do not store annotation
		//Should be blocked client side in the future TODO
		die();
	}


	$comment = new stdClass();
	$comment->id = 0; //This will be changed by the DB
	$comment->url = $_POST['url'];
	$comment->annotation_id = $_POST['annotation_id'];
	$comment->timecreated = time();
	$comment->comment = $_POST['comment'];
	$comment->userid = $USER->id;

	/*
	$table = "annotation_comment"; /the table name
	//Insert into DB and get the id
	$lastinsertid = $DB->insert_record($table, $comment);
	*/

	//Send the user name and timecreated back to the client [what about comment_id?]
	$response = new stdClass();
	$response->username = $USER->firstname . " " . $USER->lastname;
	$response->timecreated = $comment->timecreated;
	echo json_encode($response);
}
