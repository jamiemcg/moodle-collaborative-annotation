<?php
require_once('controller.php');

$method = $_SERVER['REQUEST_METHOD'];

if($method == 'GET') {
	root($_GET);
}
else if($method == 'POST') {
	create($_POST);
}
else {
	//Other methods
	//PUT, DELETE, etc...
}