<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUEMNT_ROOT'] . '/includes/autoload.php';
$user = User::get_current_user();
//$profile_id  =3;
try {
	if ($user instanceof CurrentUser) {
		$profile_id = $user->user_id;
		$mysqli = Database::connection();
		$sql = "SELECT user_id, username, reputation, date_registered, last_seen, email, avatar FROM `users` WHERE user_id = '$profile_id'";
		$result = $mysqli->query($sql)
		or die ($mysqli->error);
		if ($result->num_rows == 1) {
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$profile = new ProfileUser($row);
			http_response_code(200);
			echo json_encode($profile, JSON_PRETTY_PRINT);
		}
		else {
			throw new OutOfRangeException('OutOfRangeException occured on request');
		}
	}
	else {
		throw new UnexpectedValueException('UnexpectedValueException occured on request, no user is currently logged in');
	}
}
catch (UnexpectedValueException $e) {
	http_response_code(400);
	Database::print_exception($e);
}
catch (OutOfRangeException $e) {
	http_response_code(400);
	Database::print_exception($e);
}