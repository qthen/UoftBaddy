<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$message_text = $data->message_text;
$conversation_id = $data->conversation_id;
$user = User::get_current_user();
/*$message_text = 'FIrst post!!';
$conversation_id = 1;*/
try {
	if ($user instanceof CurrentUser) {
		if (is_numeric($conversation_id) && $message_text) {
			$message_text = Database::sanitize($message_text);
			$message = new Message(array(
				'message_text' => $message_text)
			);
			$conversation = new Conversation(array(
				'conversation_id' => $conversation_id)
			);
			$post_result = $user->post_message($conversation, $message);
			if ($post_result) {
				http_response_code(200);
				echo json_encode($post_result, JSON_PRETTY_PRINT);
			}
			else {
				throw new RuntimeException('RuntimeException occured on request, could not post message for some reason');
			}
		}	
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on request, input parameters are incorrect');
		}
	}
	else {
		throw new UnexpectedValueException('UnexpectedValueException occured on request, user is not logged in');
	}
}
catch (UnexpectedValueException $e) {
	http_response_code(400);
	Database::print_exception($e);
}
catch (RuntimeException $e) {
	http_response_code(400);
	Database::print_exception($e);
}
?>