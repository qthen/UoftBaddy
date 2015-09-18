<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
function __autoload($class) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/$class.php";
}
$request = file_get_contents('php://input');
$data =json_decode($request);
$user_id = $data->user_id;
try {
	if (is_numeric($user_id)) {
		$mysqli = Database::connection();
		$sql = "SELECT t1.date_id, t1.datename, t1.begin_datetime, t1.end_datetime
		FROM `badminton_dates` as t1 
		INNER JOIN `joins` as t2 
		ON t2.date_id = t1.date_id 
		AND t2.user_id = '$user_id'
		WHERE t1.type != '" . UNAVAILABLE_DATE. "'";
		$result = $mysqli->query($sql)
		or die ($mysqli->error);

		$events = array();
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			switch ($row['type']) {
				case 0:
					$date = new PublicProposedDate($row); //Implemenet group functionality later
					break;
				case 1;
					$date = new ConfirmedBadmintonDate($row);
					break;
			}
			$events[] = $date;
		}
		http_response_code(200);
		echo json_encode($events, JSON_PRETTY_PRINT);
	}
	else {
		throw new UnexpectedValueException('UnexpectedValueException occured on request');
	}
}
catch (UnexpectedValueException $e) {
	http_response_code(400);
	Database::print_exception($e);
}
?>