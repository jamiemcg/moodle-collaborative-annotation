<?php

/**
 * Deletes an annotation from the server
 * Sepecified by id number in DELETE request
 * 
 * 
 */
 

 if(!empty($_POST['id']) && !empty($_POST['userid'])) {
	require_once(__DIR__ . "../../../../config.php");
	require_login();


	global $CFG, $DB, $USER;

	$userid = $USER->id; //Gets the current users id

	$annotation_id = $_POST['id'];
	$annotation_userid = $_POST['userid'];

	//Ensure the current user created the annotation
	if($annotation_userid == $userid) {
		$param = new stdClass();
		$params = array(
						"id" => $annotation_id,
						"userid" => $userid
					   );

		$table = "annotation_image";
		$DB->delete_records($table, $params);
		echo "1";
	}
	else {
		echo "0";
	}
}
else {
	echo "0";
}