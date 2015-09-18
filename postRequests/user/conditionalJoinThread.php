<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$start_conditional = $data->start_conditional;
$end_conditional = $data->end_conditional;
$user = User::get_current_user();
try {
	if ($user instanceof User) {
		if ($start_conditional && $end_conditional) {
			$join_id = $user->join_thread($thread, array('begin_conditional' => $start_conditional, 'end_conditional' => $end_conditional));
			if ($join_id) {
				http_response_code(200);
			}
			else {
				throw new RuntimeException('RuntimeException occured on request, could not join thread for some reason');
			}
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on request');
		}
	}
	else {
		throw new UnexpectedValueException('UnexpectedValueException occured on request, no user is currently logged in');
	}
}
catch (Eexception $e) {
	http_response_code(400);
	Database::print_exception($e);
}