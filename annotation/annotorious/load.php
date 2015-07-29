 <?php

/**
 * Loads the annotations from the server for the image currently being viewed.
 * GET request with the window.location.url
 * 
 */
if(!empty($_POST)) {
	require_once(__DIR__ . "../../../../config.php");
	require_login();


	global $CFG, $DB, $USER;

	$table = 'mdl_annotation_image';
	$url = $_POST['url'];

	$sql = "SELECT * FROM mdl_annotation_image WHERE url = ?";
	$rs = $DB->get_recordset_sql($sql, array($url));
	
	//Loop through results
	foreach($rs as $record) {
		print_object($record);
	}
	$rs->close();
}