<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . 'autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$date_id = $data->date_id;
try {
	$user = User::get_current_user();
	if (is_numeric($date_id) && ($user instanceof CurrentUser)) {
		$date = new SomeDate(array(
			'date_id' => $date_id)
		);
		$date->get_times();
		$result = $user->leave_date($date);
		if ($result) {
			http_response_code(200);
		}
		else {
			throw new OutOfBoundsException('OutOfBoundsException occured on request, the left deadline has already passed');
		}
	}
	else {
		throw new UnexpectedValueException('UnexpectedValueException occured on request, parameters are not numeric or no user is not logged in currently');
	}
}
catch (Exception $e) {
	http_response_code(400);
	Database::print_exception($e);
}