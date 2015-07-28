<?php

/**
 * Stores an annotation with the data received from a POST request
 * Must attatch timestamp, id, user_id to the annotation
 * Returns the id of the created annotation
 * 
 */

global $CFG, $DB, $USER;
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');

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