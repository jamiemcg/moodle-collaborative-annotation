 <?php

/**
 * Loads the annotations from the server for the image currently being viewed.
 * POST request with the window.location.url (cmid)
 */
if(!empty($_POST['url'])) {
	require_once(__DIR__ . "../../../../config.php");
	require_login();

	global $CFG, $DB, $USER;

	$userid = $USER->id; //Gets the current users id

	$table = 'annotation_image';
	$url = $_POST['url'];
	$cmid = $url;

	require_once("../initialize.php");

	//Check if group annotations are enabled and if group visibility is enabled
	if($group_annotation && ! $group_annotations_visible && $group != -1) {
		$sql = "SELECT * FROM mdl_annotation_image WHERE url = ? AND group_id = ? OR group_id = -1";
		$rs = $DB->get_recordset_sql($sql, array($url, $group));
	}
	else {
		//Group annotation is disabled, load all existing annotations
		$sql = "SELECT * FROM mdl_annotation_image WHERE url = ?";
		$rs = $DB->get_recordset_sql($sql, array($url));
	}
	
	$response = array();
	$response[] = $editable;

	//Loop through results
	foreach($rs as $record) {
		//Get username of annotation creator
		$user = $DB->get_record('user', array("id" =>$record->userid));
		$record->username = $user->firstname . " " . $user->lastname;
		$record->groupname = groups_get_group_name($record->group_id);
		//Enable editing of annotation only if current user created it
		if($record->userid == $userid) {
			$record->editable = true;
		}
		else {
			$record->editable = false;
		}

		$response[] = $record;
	}
	$rs->close(); //Close the record set

	echo json_encode($response);
}
else {
	http_response_code(400);
}
