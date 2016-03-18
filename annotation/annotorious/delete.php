<?php

/**
 * Deletes an annotation from the server
 * Sepecified by id number in POST request 
 */
 
 if(!empty($_POST['id'])) {
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
		$sql = "DELETE FROM mdl_annotation_image WHERE id = ? AND userid = ?";
		$DB->execute($sql, array($id, $userid));

		//Delete the comments attatched to this annotaton
		$sql = "DELETE FROM mdl_annotation_comment WHERE annotation_id=?";
    	$DB->execute($sql, array($id));

		echo "1"; //Success code
	}
	else {
		echo "0"; //Error code
	}
}
