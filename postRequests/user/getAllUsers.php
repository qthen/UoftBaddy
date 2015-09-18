<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
function __autoload($class) {
	if ($class == 'ProfileUser') {
		$class = 'User';
	}
	if ($class == 'PublicProposedDate') {
		$class = 'BadmintonDate';
	}
	require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/$class.php";
}

$request = file_get_contents('php://input');
$data = json_decode($request);
$filter = $data->filter;
$acceptable_filters = array(
	'reputation',
	'date_registered'
);
try {
	if (in_array($filter, $acceptable_filters)) {
		$mysqli = Database::connection();
		$sql = "SELECT user_id, username, reputation, avatar, email, date_registered, last_seen, program, level, commuter
		FROM users
		ORDER BY " . $filter. " DESC";
		$result = $mysqli->query($sql)
		or die ($mysqli->error);
		$users = array();
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$user = new ProfileUser($row);
			$users[] = $user;
		}
		http_response_code(200);
		echo json_encode($users, JSON_PRETTY_PRINT);
	}
	else {
		throw new UnexpectedValueException('UnexpectedValueException occured on request');
	}
}
catch (UnexpectedValueException $e) {
	http_response_code(400);
	Database::print_exception($e);
}