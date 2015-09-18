<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$date_string = $data->date;
/*$date_string = '2015-09-07';*/
try {
	if ($date_string) {
		$date_string = Database::sanitize($date_string);
		$mysqli = Database::connection();
		$sql = "SELECT t1.date_id, t1.datename, DATE_FORMAT(t1.begin_datetime, '%b %e, %Y - %r') as `begin_datetime`, DATE_FORMAT(t1.end_datetime, '%b %e, %Y - %r') as `end_datetime`, t1.creator_id as `user_id`, t2.username, t2.email, t2.reputation, t2.avatar, t1.bool_group, t1.confirmed
		FROM `badminton_dates` as t1
		INNER JOIN `users` as t2 
		ON t2.user_id = t1.creator_id
		WHERE t1.date_id = '$date_string'
		ORDER BY t1.begin_datetime ASC";
		$result = $mysqli->query($sql)
		or die ($mysqli->error);
		$dates = array();
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
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
			array_push($dates, $badminton_date);
		}
		http_response_code(200);
		echo json_encode($dates, JSON_PRETTY_PRINT);
	}
}
catch (Exception $e) {
	http_response_code(400);
	Database::print_exceptoin($e);
}
?>