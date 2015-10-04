<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$notification_id = $data->notification_id;
//$notification_id = 36;
$user = User::get_current_user();
try {
	if (is_numeric($notification_id) && $user instanceof CurrentUser) {
		$notification = new Notification(array(
			'notification_id' => $notification_id)
		);
		$result = $user->mark_notification_as_read($notification);
		http_response_code(200);
	}
	else {
		throw new UnexpectedValueException('UnexpectedValueException occured on request because the input parameters are invalid');
	}
}
catch (Exception $e) {
	http_response_code(400);
	Database::print_exception($e);
}