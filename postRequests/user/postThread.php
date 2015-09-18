<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$thread_text = $data->thread_text;
$type = $data->type;
$date_play = $data->date_play;
// $type = 2;
// $thread_text = 'ahdisahdosa';
$user = User::get_current_user();
try {
	if (array_key_exists($type, Thread::$type_contracts) && ($thread_text)) {
		if ($user instanceof CurrentUser) {
			list($thread_text)  = Database::sanitize(array($thread_text));
			$comment = new Thread(array(
				'thread_text' => $thread_text,
				'date_play' => $date_play,
				'type' => $type)
			);
			$result = $user->post_thread($comment);
			if ($result) {
				//Now if this is an anticipated date, we will try to create a tentative badminton date and denote it with a type = 2
				http_response_code(200);
				$result->thread_text = $data->thread_text;
				echo json_encode($result, JSON_PRETTY_PRINT);
			}
			else {
				throw new RuntimeException('RuntimeException occured on request, could not post commment for some reason');
			}
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on request because no detected user is logged in');
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