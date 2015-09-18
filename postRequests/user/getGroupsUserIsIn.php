<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
function __autoload($class) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/$class.php";
}
$request = file_get_contents('php://input');
$data = json_decode($request);
$user_id = $data->user_id;
try {
	if (is_numeric($user_id)) {
		$sql = "SELECT t1.group_id, t1.group_name, t1.group_description, t1.reputation
		FROM groups as t1 
		INNER JOIN group_members as t2 
		ON t2.group_id = t1.group_id
		AND t2.member_id = '$user_id'";
		$result = $mysqli->query($sql)
		or die ($mysqli->error);
		$groups = array();
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$group = new Group($row);
			array_push($groups, $group);
		}
		http_response_code(200);
		echo json_encode($groups, JSON_PRETTY_PRINT);
	}
	else {
		throw new UnexpectedValueException('UnexpectedValueException occured on request');
	}
}
catch (UnexpectedValueException $e) {
	http_response_code(400);
	Database::print_exception($e);
}