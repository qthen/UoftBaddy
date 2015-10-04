<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
function __autoload($class) {
    if ($class == 'ProposedDate' || ($class == 'BadmintonDate')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/BadmintonDate.php';
    }
    else {
		require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/$class.php";
	}
}
$request = file_get_contents('php://input');
$data = json_decode($request);
$date_id = $data->date_id;
$user = User::get_current_user();
try {
	if (($user instanceof User) && is_numeric($date_id)) {
		$badminton_date = new ConfirmedBadmintonDate(array(
			'date_id' => $date_id)
		);
		$notify_absence = $user->notify_absence($badminton_date);
		if ($notify_absence) {
			http_response_code(200);
		}
		else {
			throw new RuntimeException('RuntimeException occured on request');
		}
	}
	else {
		throw new UnexpectedValueException('UnexpectedValueException occured on request');
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