<?php

/**
 * Updates the text of an annotation. The annotation
 * is specified by an id number in a POST request.
 * 
 */

if(!empty($_POST)) {
	require_once(__DIR__ . "../../../../config.php");
	require_login();

	global $CFG, $DB, $USER;

	$id = $_POST['id'];
	$userid = $USER->id;

	$params = array(
					"id" => $id,
					"userid" => $userid
				   );

	$table ="annotation_image";
	$count = $DB->count_records($table, $params);
	//If the user logged in didn't create the annotation $count will be 0
	if($count)	 {
		$annotation = htmlentities($_POST['text']);
		$timecreated = time();
		$tags = htmlentities(json_decode($_POST['tags'])); //TODO

		$sql = "UPDATE mdl_annotation_image SET annotation = ?, timecreated = ?, tags = ? WHERE id = ? AND userid = ?";
		$DB->execute($sql, array($annotation, $timecreated, $tags, $id, $userid));

		$response = new stdClass();
		$response->id = $id;
		$response->timecreated = $timecreated;
		$response->username = $USER->firstname . " " . $USER->lastname; //Needs to be reset
		echo json_encode($response); //Return updated object to client
	}
	else {
		echo "0";
	}
}
