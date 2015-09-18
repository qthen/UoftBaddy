<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$conversation_id = $data->conversation_id;
$user = User::get_current_user();
try {
	if (is_numeric($conversation_id) && ($user instanceof CurrentUser)) {
		$conversation = new Conversation(array(
			'conversation_id' => $conversation_id)
		);
		$result = $user->close_conversation($conversation);
		if ($result) {
			http_response_code(200);
		}
		else {
			throw new RuntimeException('RuntimeException occured on request, could not close conversation for some reason');
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