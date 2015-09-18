<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$mysqli = Database::connection();
$sql = "SELECT creator_id as `user_id`, t2.events, t3.username, t3.email, t3.reputation, t3.avatar
FROM badminton_dates as t1 
INNER JOIN (
	SELECT creator_id as `user_id`, COUNT(date_id) as `events`
	FROM `badminton_dates` as t1
	GROUP BY creator_id
) as t2 
ON t2.user_id = t1.creator_id
LEFT JOIN `users` as t3 
ON t3.user_id = t1.creator_id
";
$result = $mysqli->query($sql)
or die ($mysqli->error);
$users = array();
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	$user = new ProfileUser($row);
	$user->hosted_events = $row['events'];
	array_push($users, $user);
}
http_response_code(200);
echo json_encode($users, JSON_PRETTY_PRINT);