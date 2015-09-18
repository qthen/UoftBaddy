<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$user_id = $data->user_id;
try {
	if (is_numeric($user_id)) {
		$user = new ProfileUser(array(
			'user_id' => $user_id)
		);
		$arrayOfActions = $user->get_activity();
		if ($arrayOfActions) {
			http_response_code(200);
			echo json_encode($arrayOfActions, JSON_PRETTY_PRINT);
		}
		else {
			throw new RuntimeException('RuntimeException occured on request, could not fetch activity for some reason');
		}
	}
}
catch (Exception $e) {
	http_response_code(400);
	Database::print_exception($e);
}