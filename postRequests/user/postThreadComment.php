<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$thread_id = $data->thread_id;
$comment_text = $data->comment_text;
$parent_id = $data->parent_id;
/*$thread_id = 11;
$comment_text = 'hihih hiiii';
$parent_id = null;*/
$user = User::get_current_user();
try {
	if (is_numeric($thread_id) && ($comment_text) && ($user instanceof User) && (!$parent_id XOR (is_numeric($parent_id)))) {
		$comment_text = Database::sanitize($comment_text);
		$thread = new Thread(array(
			'thread_id' => $thread_id)
		);
		$comment = new ThreadComment(array(
			'comment_text' => $comment_text,
			'thread' => $thread
			)
		);
		$result_post = $user->post_thread_comment($thread, $comment);
		if ($result_post) {
			http_response_code(200);
			echo json_encode($result_post, JSON_PRETTY_PRINT);
		}
		else {
			throw new RuntimeException('RuntimeException occured on request, could not post comment for some reason');
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