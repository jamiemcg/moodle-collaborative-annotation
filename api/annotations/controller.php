<?php

//global $CFG, $DB, $USER; //Moodle global variables
//require_login(); //Require the user to be logged into Moodle, if not redirect to homepage
/**
 * GET request to the API root
 * Returns api methods
 * @param $request - The GET request data
 */
function root($request) {
	header("HTTP/1.1 200 OK");
	echo "GET request received";
}

/**
 * Reads the data of an existing annotation
 * specified by its id number
 * @request - The GET request data (including the id)
 */
function read($request) {

}

/**
 * Processes POST request to create an annotation 
 * @param $request - The POST request data (json encoded)
 */
function create($request) {
	if (isset($_POST['annotation']) && !empty($_POST['annotation'])) {
		//The annotation data is stored in JSON form in $_POST['annotation']
		$annotation = json_decode($_POST['annotation']); //Decode the JSON string
		print_r($annotation);


		$uri = $annotation->uri; //The uri of the document that the annotation belongs to

		//Access the db, store the annotation with a unique id

		//Return this if the annotation creation was successful
		header("HTTP/1.1 201 CREATED");
	}
	else {
		echo "Send the annotation data via POST request";
	}
	//echo "Hello ". $_POST['annotation'];

}

/**
 * Deletes an annotation specified by id
 * number paramater
 * @param $request - The DELETE request data (including the id)
 */
function delete($request) {

}

/**
 * Updates a previously created annotation
 * specified by providing an id number
 * @param $request - The PUT request data (including the id)
 */
function update($request) {

}