<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
function __autoload($class) {
	if ($class == 'ProfileUser') {
		$class = 'User';
	}
	if ($class == 'PublicProposedDate') {
		$class = 'BadmintonDate';
	}
	require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/$class.php";
}
$request = file_get_contents('php://input');
$data = json_decode($request);
$user_id = $data->user_id;
//$user_id = 3;
try {
	if (is_numeric($user_id)) {
		$mysqli = Database::connection();
		$sql = "SELECT t1.date_id, t2.datename, t2.begin_datetime, t2.end_datetime, t1.status, t1.user_id, t3.email, t3.username, t3.reputation, t3.avatar
		FROM joins as t1
		LEFT JOIN `badminton_dates` as t2
		ON t2.date_id = t1.date_id
        LEFT JOIN `users` as t3 
        ON t3.user_id = t1.user_id
		WHERE t2.type != '" . UNAVAILABLE_DATE. "'
		AND t1.user_id = '$user_id'
		ORDER BY t2.begin_datetime DESC";
		//echo $sql;
		$result = $mysqli->query($sql)
		or die ($mysqli->error);

		$events = array();
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			switch ($row['type']) {
				case 0:
					$row['creator'] = new ProfileUser($row);
					$date = new PublicProposedDate($row); //Implemenet group functionality later
					break;
				case 1;
					$row['creator'] = new ProfileUser($row);
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