<?php

/**
 * Stores an annotation with the data received from a POST request
 * Must attatch timestamp, id, user_id to the annotation
 * Returns the id of the created annotation
 * 
 */


require_once(__DIR__ . "../../../../config.php");
require_login();


global $CFG, $DB, $USER;

print_r($_POST);


echo $USER->firstname . " " . $USER->lastname;
echo $USER->id;

//Create a new annotation object
//Populate it with *some* of the information from the POST request
//Add other data -> timestamp, user, etc...
//Store it in the DB
//Return the ID to the client
//Return the timecreated and user to the client for display


//echo $_POST['text'];
