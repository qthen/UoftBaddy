<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$date_id = $data->date_id;
$user = User::get_current_user();
try {
	if (is_numeric($date_id) && ($user instanceof User)) {
		$badminton_date = new PublicProposedDate(array(
			'date_id' => $date_id)
		);
		if (!$badminton_date->is_confirmed) {
			if ($user->in_date($badminton_date)) {
				$result = $user->confirm_badminton_date($badminton_date);
				if ($result) {
					http_response_code(200);
				}
				else {
					throw new RuntimeException('RuntimeException occured while trying to confirm this date, dont know why');
				}
			}
			else {
				throw new OutOfBoundsException('OutOfBoundsException occured on request, current user in not in the badminton date');
			}
		}
		else {
			http_response_code(200);
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
catch (OutOfBoundsException $e) {
	http_response_code(400);
	Database::print_exception($e);
}
catch (RuntimeException $e) {
	http_response_code(400);
	Database::print_exception($e);
}
?>