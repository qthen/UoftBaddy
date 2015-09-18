<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$message_text = $data->message_text;
$parent_id = $data->parent_id;
$user = User::get_current_user();
try {
	if (is_numeric($parent_id) && ($message_text) && ($user instanceof User)) {
		$message_text = Database::sanitize($message_text);
		$mysqli = Database::connection();
		$result = $user->post_reply($message, $reply);
		if ($result) {
			$result->message_text = $data->message_text;
			echo json_encode($result, JSON_PRETTY_PRINT);
			http_response_code(200);
		}
		else {
			throw new RuntimeException('RuntimeException occured on request, could not insert reply for some reason');
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