<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$date_id = $data->date_id;
//$date_id = 19;
$user = User::get_current_user();
try {
	if (($user instanceof User) && (is_numeric($date_id))) {
		$badminton_date = new PublicProposedDate(array(
			'date_id' => $date_id)
		);
		$badminton_date->get_datename();
		$result = $user->join_badminton_date($badminton_date);
		if ($result) {
			http_response_code(200);
		}
		else {
			throw new RuntimeException('RuntimeException occured on request, could not join badminton date for some reason');
		}
	}
	else {
		throw new UnexpectedValueException('UnexpectedValueException occured on request');
	}
}
catch (Exception $e) {
	http_response_code(400);
	Database::print_exception($e);
}