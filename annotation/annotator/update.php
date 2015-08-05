<?php

/**
 * Updates an annotation with the data received from a POST request
 * Must attatch timestamp to the annotation
 * Returns the new annotation object
 */

if(!empty($_POST)) {
	require_once(__DIR__ . "../../../../config.php");
	require_login();

	global $CFG, $DB, $USER;

	$userid = $USER->id;
	$id = $_POST['id'];

	$timecreated = time();
	$annotation = htmlentities($_POST['text']);

	if(isset($_POST['tags']) && !empty($_POST['tags'])) {
		$tags = json_encode($_POST['tags']);
	}
	else {
		$tags = NULL; //Uses null for no tags instead of empty string
	}

	$params = array(
					"id" => $id,
					"userid" => $userid
				   );

	$table ="annotation_annotation";
	$count = $DB->count_records($table, $params);
	//If the user logged in didn't create the annotation $count will be 0
	if($count)	 {
		//Save changes to the database
		$sql = "UPDATE mdl_annotation_annotation SET timecreated = ?, annotation = ?, tags = ? WHERE id = ? AND userid = ?";
		$DB->execute($sql, array($timecreated, $annotation, $tags, $id, $userid));
		
		//Return the new time
		echo $timecreated;
	}
	else {
		echo "0"; //Return error response
	}
}
else {
	http_response_code(400);
}
