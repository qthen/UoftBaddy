<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
function __autoload($class) {
	if ($class == 'ProfileUser') {
		$class = 'User';
	}
	if ($class == 'PublicProposedDate') {
		$class = 'BadmintonDate';
	}
	if (($class == 'Message') || ($class == 'Conversation')) {
		$class = 'WebMessage';
	}
	require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/$class.php";
}
$mysqli = Database::connection();
$sql = "SELECT date_id, datename, begin_datetime, end_datetime 
FROM `badminton_dates`
WHERE DATE(begin_datetime) >= CURDATE()";
$result = $mysqli->query($sql)
or die ($mysqli->error);
$dates = array();
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	$badminton_date = new BadmintonDate($row);
	$badminton_date->get_attendees();
	array_push($dates, $badminton_date);
}
http_response_code(200);
echo json_encode($dates, JSON_PRETTY_PRINT);
?>