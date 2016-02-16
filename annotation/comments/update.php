<?php

/*
 * Updates a comment with data receieve from POST request
 * POST request contains: (comment) id, comment, url (url needed?)
 * Timestamp (timecreated) should be updated
 * New information is returned to client
 */

if(!empty($_POST)) {
	require_once(__DIR__ . "../../../../config.php");
	require_login();

	global $CFG, $DB, $USER;

	$user_id = $USER->id;
	$id = $_POST['id']; //The comment id
	$comment = htmlentities($_POST['comment']);

	$timecreated = time(); //new time, timecreated isn't a good name

	$params = array("id" => $id, "user_id" =>$user_id);
	$table = "annotation_comment";
	$count = $DB->count_records($table, $params);

	//Count will be TRUE if the current user created the original comment
	if($count) {
		$sql = "UPDAT mdl_annotation_comment SET timecreated = ?, comment = ? WHERE id = ? AND user_id = ?";
		$DB->execute($sql, array($timecreated, $comment, $id, $user_id));

		echo $timecreated; //Return the new time
	}
	else {
		echo "0"; //Return error response (not successful or else wrong user)
	}
}
else {
	http_response_code(400);
}
