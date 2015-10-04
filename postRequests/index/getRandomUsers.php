<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$mysqli = Database::connection();
$sql = "SELECT user_id, username, avatar_link
FROM `users` 
ORDER BY RAND()
LIMIT 10";
$result = $mysqli->query($sql)
or die ($mysqli->error);
$users = array();
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	$user = new ProfileUser($row);
	array_push($users, $user);
}
http_response_code(200);
echo json_encode($users, JSON_PRETTY_PRINT);