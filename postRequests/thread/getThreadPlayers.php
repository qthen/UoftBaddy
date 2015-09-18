<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$mysqli = Database::connection();
$sql = "SELECT DISTINCT(t1.author_id) as `user_id`, t2.username, t2.email, t2.reputation, t2.avatar
FROM `threads` as t1
INNER JOIN `users` as t2 
ON t2.user_id = t1.author_id
WHERE t1.type = '" . Thread::LOOKING_TO_PLAY . "'
AND t1.date_play > NOW()";
$result = $mysqli->query($sql)
or die ($mysqli->error);
//echo $sql;
$players = array();
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	$player = new ProfileUser($row);
	array_push($players, $player);
}
http_response_code(200);
echo json_encode($players, JSON_PRETTY_PRINT);