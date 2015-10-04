<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$mysqli = Database::connection();
$sql = "SELECT COUNT(*) FROM users";
$result = $mysqli->query($sql)
or die ($mysqli->error);
$total_users = $result->fetch_row()[0];
$sql = "SELECT COUNT(*) FROM users WHERE date_registered = CURDATE()";
$result = $mysqli->query($sql)
or die ($mysqli->error);
$new_users = $result->fetch_row()[0];
$array = array(
	'total_users' => $total_users,
	'new_users_today' => $new_users
);
http_response_code(200);
echo json_encode($array, JSON_PRETTY_PRINT);