<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
function __autoload($class) {
	if ($class == 'ProfileUser') {
		$class = 'User';
	}
	require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/$class.php";
}
$request = file_get_contents('php://input');
$data = json_decode($request);
$profile_id = $data->profile_id;
//$profile_id  = 1069062649779910;
try {
	if (is_numeric($profile_id)) {
		$mysqli = Database::connection();
		$sql = "SELECT user_id, username, reputation, date_registered, avatar_link, last_seen, email, avatar, commuter, program, level, avatar_link, bio FROM `users` WHERE user_id = '$profile_id'";
		$result = $mysqli->query($sql)
		or die ($mysqli->error);
		if ($result->num_rows == 1) {
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$profile = new ProfileUser($row);
			$profile->get_number_of_joins();
			$profile->get_number_of_leaves();
			$profile->get_number_of_hosted_events();

			$denominator = ($profile->number_of_leaves == 0) ? 1 : $profile->number_of_leaves;
			$profile->join_leave_ratio = $profile->number_of_joins / $denominator;
			
			http_response_code(200);
			echo json_encode($profile, JSON_PRETTY_PRINT);
		}
		else {
			throw new OutOfRangeException('OutOfRangeException occured on request');
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
catch (OutOfRangeException $e) {
	http_response_code(400);
	Database::print_exception($e);
}