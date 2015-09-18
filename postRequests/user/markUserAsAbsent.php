<?php
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
	if (($user instanceof CurrentUser) && is_numeric($user_id) && is_numeric($date_id)) {
		//Check to see if the user is in the joined badminton date
		
		$user_a = new User(array(
			'user_id' => $user_id)
		);
		$badminton_date = new ConfirmedBadmintonDate(array(
			'date_id' => $date_id)
		);

		$user_in_date = $user->in_date($badminton_date);
		$user_a_in_date = $user_a->in_date($badminton_date);
		if ($user_in_date && $user_a_in_date) {
			//Both the user and the user in question are in the date
			$mark_absent = $user->mark_user_as_absent($user_a, $badminton_date);
			if ($mark_absent) {
				http_response_code(200);
			}
			else {
				throw new RuntimeException('RuntimeException occured on marking the user absent');
			}
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on request');
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