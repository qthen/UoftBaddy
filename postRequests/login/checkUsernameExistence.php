<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
function __autoload($class_name) {
	/*
	Last chance for PHP script to call a class name
	 */
	$class_name = ($class_name == 'OutgoingTranslation' || $class_name == 'IncomingTranslation') ? 'Translate' : $class_name;
	require_once $_SERVER['DOCUMENT_ROOT']. "/includes/$class_name.php";
}
$request = file_get_contents('php://input');
$data = json_decode($request);
$username = $data->username;
/*$username = 'HIHI';*/
require_once __DIR__ . '/../../includes/Database.php';
try {
	if ($username) {
		$username = Database::sanitize($username); //Sanitize the user input
		$mysqli = Database::connection(); //Initiate the Mysqli Connection variable
		$sql = "SELECT user_id FROM users WHERE username = '$username'";
		$result = $mysqli->query($sql)
		or die ($mysqli->error);
		if ($result->num_rows > 0) {
			http_response_code(400);
		}
		else {
			http_response_code(200); //The username does not yet exist
		}
	}
	else {
		throw new UnexpectedValueException('UnexpectedValueException');
	}
}
catch (UnexpectedValueException $e) {
	Database::print_exception($e);
	http_response_code(400);
}