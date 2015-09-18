<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information 
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$user = User::get_current_user();
try {
	if ($user instanceof User) {
		$mysqli = Database::connection();
		//The user is logged in, get the dropdown for the user
		$sql = "SELECT user_id, username, reputation, avatar, avatar_link FROM `users` WHERE user_id = '$user->user_id'";
		$result = $mysqli->query($sql)
		or die ($mysqli->error);
		if ($result->num_rows == 1) {
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$user = new ProfileUser($row);

			//Get the dropdown fields
			$sql = "SELECT COUNT(*) as `upcoming_confirmed_badminton_dates`
			FROM `joins` as t1 
			LEFT JOIN `badminton_dates` as t2 
			ON t2.date_id = t1.date_id
			WHERE t2.begin_datetime > NOW()
			AND t1.user_id = '$user->user_id'
			AND t2.confirmed = '" . BadmintonDate::CONFIRMED . "'";
			$result = $mysqli->query($sql)
			or die ($mysqli->error);
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			//echo $sql;
			$sql = "SELECT COUNT(*) as `upcoming_threads_joined`
			FROM `joins` as t1 
			INNER JOIN `badminton_dates` as t2 
			ON t2.date_id = t1.date_id
			WHERE t2.begin_datetime >= NOW()
			AND t2.confirmed = '" . BadmintonDate::TENTATIVE . "'";
			$result = $mysqli->query($sql)
			or die ($mysqli->error);
			$row_threads = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$rows_combined = array_merge($row, $row_threads);

			$return = array(
				'user' => $user,
				'fields' => $rows_combined
			);

			http_response_code(200);
			echo json_encode($return, JSON_PRETTY_PRINT);
		}
		else {
			throw new OutofRangeException('OutofRangeException occured on request, the user does not exist');
		}
	}
	else {
		throw new UnexpectedValueException('UnexpectedValueException occured on request, no user is currently logged in');
	}
}
catch (Exception $e) {
	http_response_code(400);
	Database::print_exception($e);
}
?>