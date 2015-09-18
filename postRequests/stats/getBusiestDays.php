<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
function __autoload($class) {
    if ($class == 'ProposedDate' || ($class == 'BadmintonDate')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/BadmintonDate.php';
    }
    else {
		require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/$class.php";
	}
}
$mysqli = Database::connection();
$sql = "SELECT DAY(datetime) as `day`, COUNT(*) as `dates`
FROM `badminton_dates`
GROUP BY `day`";
$result = $mysqli->query($sql)
or die ($mysqli->error);
$array = array();
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	$array[$row['day']] = $row['dates'];
}
http_response_code(200);
echo json_encode($array, JSON_PRETTY_PRINT);
?>