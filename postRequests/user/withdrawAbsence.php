<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$date_id = $data->date_id;
$user = User::get_current_user();
//$date_id = 18;
try {
	if (is_numeric($date_id)) {
		$date = new SomeDate(array(
			'date_id' => $date_id)
		);
		$result = $user->withdraw_absence($date);
		if ($result) {
			http_response_code(200);
		}
		else {
			throw new RuntimeException('RuntimeException occured on request, could not re-join for some reason');
		}
	}
	else {
		throw new UnexpectedValueException('UnexpectedValueException occured on request since the date id is invalid');
	}
}
catch (Exception $e) {
	http_response_code(400);
	Database::print_exception($e);
}