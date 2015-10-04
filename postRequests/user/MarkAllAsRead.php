<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';;
$user = User::get_current_user();
try {
	if ($user instanceof CurrentUser) {
		$mysqli = Database::connection();
		$sql = "UPDATE notifications SET read_status = '" . NotificationFactory::READ . "' WHERE user_id = '$user->user_id'";
		$result = $mysqli->query($sql)
		or die ($mysqli->error);
		http_response_code(200);
	}
	else {
		throw new UnexpectedValueException('UnexpectedValueException occured on request since no user is currently logged in');
	}
}
catch (Exception $e) {
	http_response_code(400);
	Database::print_exception($e);
}