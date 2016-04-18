<?php

/**
 * Deletes an annotation from the server
 * Sepecified by id number in POST request 
 */
 
 if(!empty($_POST['id'])) {
	require_once(__DIR__ . "../../../../config.php");
	require_login();

	global $CFG, $DB, $USER;

	$userid = $USER->id; //Gets the current users id
	$annotation_id = $_POST['id'];

	$params = array(
					"id" => $annotation_id,
					"userid" => $userid
				   );

	$table = "annotation_annotation";
	$count = $DB->count_records($table, $params);
	
	//If the user logged in didn't create the annotation $count will be 0
	if($count) {
		$result = $DB->delete_records($table, $params);

		//Delete the comments attatched to this annotaton
		$sql = "DELETE FROM mdl_annotation_comment WHERE annotation_id=?";
    	$DB->execute($sql, array($annotation_id));

		echo "1"; //Return success response
	}
	else {
		echo "0"; //Return failure response
	}
}
else {
	http_response_code(400);
}
