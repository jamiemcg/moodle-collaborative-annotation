<?php

require_once('locallib.php');

/**
 * Checks the timing settings from the DB.
 * Returns false if time is up and causes a redirect
 */
function check_time_constraint_db() {

	//Presume we have access to $cmid from the script that called this
	//Load the time settings from the database incase they were changed since the page loaded
	$table = "annotation_document";
	$results = $DB->get_records($table, array('cmid' => $cmid));
	foreach ($results as $result) {
	        $allow_from = $result->allow_from;
	        $allow_until = $result->allow_until;
	        break; //Bad way of doing this
	}


	if(check_time_constraint($allow_from, $allow_until)) {
		return true;
	}
	else {
		//The file is no longer annotatable
		return false;
	}
}