<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$comment_text = $data->comment_text;
$type = $data->type;
$parent_id = $data->parent_id;
$user = User::get_current_user();
try {
	if (array_key_exists($type, GeneralComment::$type_contracts) && ($comment_text) && ($user instanceof User)) {
		if (!$parent_id) {
			$comment_text = Database::sanitize($comment_text);
			$comment = new GeneralComment(array(
				'comment_text' => $comment_text,
				'type' => $type)
			);
			$result = $user->post_general_comment($comment);
			if ($result) {
				http_response_code(200);
				$result->comment_text = $data->comment_text;
				echo json_encode($result, JSON_PRETTY_PRINT);
			}
			else {
				throw new RuntimeException('RuntimeException occured on request, could not post commment for some reason');
			}
		}
		else {
			//This comment is a reply, treat it as such
			$comment_text = Database::sanitize($comment_text);
			$parent = new GeneralComment(array(
				'comment_id' => $parent_id)
			);
			$comment = new GeneralComment(array(
				'parent' => $parent,
				'comment_text' => $comment_text)
			);
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