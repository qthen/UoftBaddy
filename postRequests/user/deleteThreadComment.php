<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$comment_id = $data->comment_id;
$user = User::get_current_user();
try {
	if ($user instanceof CurrentUser) {
		$comment = new ThreadComment(array(
			'comment_id' => $comment_id)
		);
		$result = $user->delete_thread_commend($thread_comment);
		if ($result) {
			http_response_code(200);
		}
		else {
			throw new RuntimeException('RuntimeException occured on request, could not delete comment for some reason');
		}
	}
	else {
		throw new UnexpectedValueException('UnexpectedValueException occured on request, the input parameters are invalid');
	}
}
catch (Exception $e) {
	http_response_code(400);
	Database::print_exception($e);
}