 <?php

/**
 * Loads the annotations from the server for the image currently being viewed.
 * GET request with the window.location.url
 */

if(!empty($_POST['url'])) {
	require_once(__DIR__ . "../../../../config.php");
	require_login();

	global $CFG, $DB, $USER;

	$userid = $USER->id; //Gets the current users id

	$url = $_POST['url'];

	$cmid = $url;
	$table = "annotation_document";
	$results = $DB->get_records($table, array('cmid' => $cmid));
	foreach ($results as $result) {
	        $group_annotation = $result->group_annotation;
	        $group_annotations_visible = $result->group_annotations_visible;
	        break; //Bad way of doing this
	}

	if($group_annotation && ! $group_annotations_visible) {
		//Get the current users group id
		
		
		$group_id = 0;

		//Load only annotations for this group
		$sql = "SELECT * FROM mdl_annotation_annotation WHERE url = ? AND group_id = ?" ;
		$rs = $DB->get_recordset_sql($sql, array($url, $group_id));
	}
	else {
		$sql = "SELECT * FROM mdl_annotation_annotation WHERE url = ?";
		$rs = $DB->get_recordset_sql($sql, array($url));
	}

	$annotations = array();

	//Loop through results
	foreach($rs as $record) {
		//Get username of annotation creator
		$user = $DB->get_record('user', array("id" =>$record->userid));
		$record->username = $user->firstname . " " . $user->lastname;
		unset($record->userid); //Don't send the user's id, not required
		
		if($record->tags == "") {
			$record->tags = null;
		}
		//TODO: Disable editing if the current user didn't create the annotation

		$annotations[] = $record;
	}
	$rs->close(); //Close the record set

	echo json_encode($annotations);
}
else {
	http_response_code(400);
}
