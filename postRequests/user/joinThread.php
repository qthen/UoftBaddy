<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$thread_id = $data->thread_id;
$user = User::get_current_user();
$thread_id = 1;
try {
	if (is_numeric($thread_id)) {
		if ($user instanceof User) {
			$thread = new Thread(array(
				'thread_id' => $thread_id)
			);
			$result =$user->join_thread($thread, array());
			if ($result) {
				http_response_code(200);
			}
			else {
				throw new RuntimeException('RuntimeException occured on request, could not join for some reason');
			}
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on request, no user is currently logged in');
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