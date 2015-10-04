<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$mysqli = Database::connection();
$sql = "SELECT COUNT(*) as `number_of_users`
FROM users";
$result = $mysqli->query($sql)
or die ($mysqli->error);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
http_response_code(200);
echo json_encode($row, JSON_PRETTY_PRINT);
?>