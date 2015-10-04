<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$mysqli = Database::connection();
$sql = "SELECT DISTINCT t2.user_id, t3.username, t3.avatar_link, t3.reputation, t3.email
FROM `badminton_dates` as t1 
LEFT JOIN `joins` as t2
ON t2.date_id = t1.date_id
INNER JOIN `users` as t3
ON t3.user_id = t2.user_id
WHERE t1.begin_datetime >= NOW()";
$result = $mysqli->query($sql)
or die ($mysqli->error);
$users = array();
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	$user = new ProfileUser($row);
	array_push($users, $user);
}
http_response_code(200);
echo json_encode($users, JSON_PRETTY_PRINT);