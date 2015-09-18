<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$mysqli = Database::connection();
$sql = "SELECT COUNT(*) 
FROM threads";
$result = $mysqli->query($sql)
or die ($mysqli->error);
$total_threads = $result->fetch_row()[0];

$sql = "SELECT COUNT(*) FROM threads WHERE WEEKOFYEAR(date_play) = WEEKOFYEAR(CURDATE())";
$result = $mysqli->query($sql)
or die ($mysqli->error);
$total_this_week = $result->fetch_row()[0];

http_response_code(200);
echo json_encode(array(
	'total_threads' => $total_threads,
	'total_this_week' => $total_this_week), JSON_PRETTY_PRINT);

