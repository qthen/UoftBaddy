<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
/*
Attempts to join a conversation based on either the conversation id or the date id
 */
$data = json_decode($request);
$date_id = $data->date_id;
$conversation_id = $data->conversation_id;
$user = User::get_current_user();
try {
	if (($user instanceof User) && (is_numeric($date_id)) XOR is_numeric($conversation_id)) {
		if (is_numeric($date_id)) {
			$sql = "SELECT conversation_id FROM `badminton_dates` WHERE date_id = '$date_id'";
			$result = $mysqli->query($sql)
			or die ($mysqli->error);
			if ($result->num_rows == 1) {
				$conversation_id = $result->fetch_row()[0];
			}
			else {
				throw new OutOfRangeException('OutOfRangeException occured on request, the date does not exist');
			}
		}
		$conversation = new Conversation(array(
			'conversation_id' => $conversation_id)
		);
		$result = $user->join_conversation($conversation);
		if ($result) {
			http_response_code(200);
		}
		else {
			throw new OutOfBoundsException('OutOfBoundsException occured on request, could not join conversation');
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