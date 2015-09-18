<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$date_id = $data->date_id;
try {
	if (is_numeric($date_id)) {
		$mysqli = Database::connection();
		$sql = "SELECT t1.date_id, t1.datename, t1.begin_datetime, t1.end_datetime, t1.creator_id as `user_id`, t2.username, t2.email, t2.reputation, t2.avatar, t1.confirmed, t1.bool_group, t3.status, t1.max_attendants
		FROM `badminton_dates` as t1 
		LEFT JOIN users as t2 
		ON t2.user_id = t1.creator_id
		LEFT JOIN `joins` as t3 
		ON t3.date_id = t1.date_id
		WHERE t1.date_id = '$date_id'";
		$result = $mysqli->query($sql)
		or die ($mysqli->error);
		if ($result->num_rows == 1) {
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$row['creator'] = new ProfileUser($row);
			if ($row['confirmed']) {
				$badminton_date = new ConfirmedBadmintonDate($row);
			}
			else {
				if ($row['bool_group']) {
					$badminton_date = new GrouProposedDate($row);
				}
				else {
					$badminton_date = new PublicProposedDate($row);
				}
			}
			$badminton_date->get_attendees();
			http_response_code(200);
			echo json_encode($badminton_date, JSON_PRETTY_PRINT);
		}
		else {
			throw new OutOfRangeException('OutOfRangeException occured on request, date could not be found');
		}
	}
	else {
		throw new UnexpectedValueException('UnexpectedValueException occured on request');
	}
}
catch (OutOfRangeException $e) {
	http_response_code(400);
	Database::print_exception($e);
}
catch (UnexpectedValueException $e) {
	http_response_code(400);
	Database::print_exception($e);
}