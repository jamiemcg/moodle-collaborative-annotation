<?php

/*
	Receive a comment_id in a POST request
	Check if the current user created that comment, if so, delete it
	What about overriding for Teachers, Tutors and Admin??
*/

if(!empty($_POST['id'])) {
	require_once(__DIR__ . "../../../../config.php");
	require_login();

	global $CFG, $DB, $USER;

	$user_id = $USER->id; //Gets the current users id
	$comment_id = $_POST['id']; //The id of the comment

	$params = array(
					"id" => $comment_id,
					"user_id" => $user_id
				   );

	$table = "annotation_comment";
	$count = $DB->count_records($table, $params);
	
	//If the user logged in didn't create the comment $count will be 0
	if($count) {
		$result = $DB->delete_records($table, $params);
		echo "1"; //Return success response (i.e. comment is deleted)
	}
	else {
		echo "0"; //Return failure response (user didn't create the comment)
	}
}
else {
	http_response_code(400);
}