<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$mysqli = Database::connection();
$sql = "SELECT COUNT(t1.date_id)
FROM `badminton_dates` as t1 
WHERE DATE(t1.begin_datetime) = CURDATE()";
$result = $mysqli->query($sql)
or die ($mysqli->error);
$today_courts = $result->fetch_row()[0];

$sql = "SELECT COUNT(t1.date_id)
FROM badminton_dates as t1 
WHERE DATE(t1.begin_datetime) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
$result = $mysqli->query($sql)
or die ($mysqli->error);
$tomorrow_courts = $result->fetch_row()[0];
http_response_code(200);
echo json_encode(array(
	'today' => $today_courts,
	'tomorrow' => $tomorrow_courts)
);

