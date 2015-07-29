 <?php

/**
 * Loads the annotations from the server for the image currently being viewed.
 * GET request with the window.location.url
 * 
 */
if(!empty($_POST['url'])) {
	require_once(__DIR__ . "../../../../config.php");
	require_login();

	global $CFG, $DB, $USER;

	$userid = $USER->id; //Gets the current users id

	$table = 'annotation_image';
	$url = $_POST['url'];

	$sql = "SELECT * FROM mdl_annotation_image WHERE url = ?";
	$rs = $DB->get_recordset_sql($sql, array($url));
	
	$annotations = array();

	//Loop through results
	foreach($rs as $record) {
		//Enable editing of annotation only if current user created it
		if($record->userid == $userid) {
			$record->editable = true;
		}
		else {
			$record->editable = false;
		}

		$annotations[] = $record;
	}
	$rs->close();

	echo json_encode($annotations);
}
