<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$user = User::get_current_user();
try {
	if ($user instanceof User) {
		$sql = "SELECT COUNT(*) as `upcoming_confirmed_badminton_dates`
		FROM `joins` as t1 
		LEFT JOIN `badminton_dates` as t2 
		ON t2.date_id = t1.date_id
		WHERE t1.begin_datetime > NOW()
		AND t1.confirmed = '" . BadmintonDate::CONFIRMED . "'";
		$result = $mysqli->query($sql)
		or die ($mysqli->error);
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$sql = "SELECT COUNT(*) as `upcoming_threads_joined`
		FROM `joins` as t1 
		INNER JOIN `badminton_dates` as t2 
		ON t2.date_id = t1.date_id
		WHERE t1.begin_datetime >= NOW()
		AND t1.confirmed = '" . BadmintonDate::TENTATIVE . "'";
		$result = $mysqli->query($sql)
		or die ($mysqli->error);
		$row_threads = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$return = array_combine($row, $row_threads);
		http_response_code(200);
		echo json_encode($return, JSON_PRETTY_PRINT);
	}
	else {
		throw new UnexpectedValueException('UnexpectedValueException occured on request, no user is currently logged in');
	}
}
catch (Exception $e) {
	http_response_code(400);
	Database::print_exception($e);
}