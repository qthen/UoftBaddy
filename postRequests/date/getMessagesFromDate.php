<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
header("Content-Type: application/json"); //Set header for outputing the JSON information
$request = file_get_contents('php://input');
$data =json_decode($request);
$date_id  = $data->date_id;
//	$date_id = 13;
$user = User::get_current_user();
try {
	if (is_numeric($date_id)) {
		$mysqli = Database::connection();
		$sql = "SELECT conversation_id, conversation_name, closed, date_started, date_recent_activity, header, date_id, joinable  FROM `conversations` WHERE date_id ='$date_id'";
		$result = $mysqli->query($sql)
		or die ($mysqli->error);
		if ($result->num_rows == 1) {
			$conversation_id = mysqli_fetch_row($result)[0];
			$conversation = new Conversation(array(
				'conversation_id' => $conversation_id)
			);
			$conversation->get_messages();
			$is_admin = $conversation->user_admin($user);
			$conversation->current_user_admin = $is_admin;
			http_response_code(200);
			echo json_encode($conversation, JSON_PRETTY_PRINT);
		}
		else {
			throw new OutOfRangeException('OutOfRangeException occured on request, conversation does not exist');
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