<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
function __autoload($class) {
	if ($class == 'ProfileUser') {
		$class = 'User';
	}
	if ($class == 'PublicProposedDate') {
		$class = 'BadmintonDate';
	}
	if (($class == 'Message') || ($class == 'Conversation')) {
		$class = 'WebMessage';
	}
	require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/$class.php";
}
$request = file_get_contents('php://input');
$data = json_decode($request);
$conversation_id = $data->conversation_id;
/*$conversation_id = 1;*/
try {
	if (is_numeric($conversation_id)) {
		$mysqli = Database::connection();
		$sql = "SELECT t1.message_id, t1.message_text, t1.author_id, t1.date_posted, t2.username, t2.reputation, t2.avatar, t2.email
		FROM `conversation_messages` as t1 
		INNER JOIN users as t2 
		ON t2.user_id = t1.author_id
		WHERE t1.conversation_id = '$conversation_id'
		ORDER BY t1.date_posted";
		$result = $mysqli->query($sql)
		or die ($mysqli->error);
		$conversation = new Conversation(array(
			'conversation_id' => $conversation_id)
		);
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$row['author'] = new ProfileUser($row);
			$message = new Message($row);
			$conversation->messages[] = $message;
		}
		http_response_code(200);
		echo json_encode($conversation, JSON_PRETTY_PRINT);
	}
	else {
		throw new UnexpectedValueException('UnexpectedValueException occured on request, not CurrentUser found');
	}
}
catch (UnexpectedValueException $e) {
	http_response_code(400);
	Database::print_exception($e);
}
?>