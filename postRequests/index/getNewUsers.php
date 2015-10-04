<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
// $request = file_get_contents('php://input');
// $data = json_decode($request);
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$mysqli = Database::connection();
$sql = "SELECT user_id, username, avatar_link, date_registered FROM `users` ORDER BY date_registered DESC LIMIT 10";
$result = $mysqli->query($sql)
or die ($mysqli->error);
$users = array();
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	$user = new ProfileUser($row);
	$users[] = $user;
}
http_response_code(200);
echo json_encode($users, JSON_PRETTY_PRINT);
?>