<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information 
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$thread_id = $data->thread_id;
//$thread_id = 1;
try {
	if (is_numeric($thread_id)) {
		$thread = new Thread(array(
			'thread_id' => $thread_id)
		);
		$result = $thread->get_all_participants();
		http_response_code(200);
		echo json_encode($result, JSON_PRETTY_PRINT);
	}
	else {
		throw new UnexpectedValueException('UnexpectedValueException occured on request, this thread id is not valid');
	}
}
catch (Exception $e) {
	http_response_code(400);
	Database::print_exception($e);
}