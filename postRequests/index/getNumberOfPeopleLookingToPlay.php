<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$mysqli = Database::connection();
$sql = "SELECT COUNT(thread_id) as `looking_to_play`
FROM threads
WHERE WEEKOFYEAR(date_play) = WEEKOFYEAR(CURDATE())";
$result = $mysqli->query($sql)
or die ($mysqli->error);
http_response_code(200);
echo json_encode(mysqli_fetch_array($result, MYSQLI_ASSOC), JSON_PRETTY_PRINT);