<?php
session_start();
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$user = User::get_current_user();
try {
	if ($user instanceof CurrentUser) {
		//User is logged in, fetch his/her inbox
		$mysqli = Database::connection();
		$sql = "SELECT t1.conversation_id, t2.conversation_name, DATE_FORMAT(t2.date_started, '%b %e, %Y - %r') as `date_started`, DATE_FORMAT(t2.date_recent_activity, '%b %e, %Y - %r') as `date_recent_activity`, t2.header
		FROM `conversation_members` as t1
		INNER JOIN `conversations` as t2
		ON t2.conversation_id = t1.conversation_id
		WHERE t1.user_id = '$user->user_id'
		ORDER BY t2.date_recent_activity DESC
		";
		$result = $mysqli->query($sql)
		or die ($mysqli->error);
		$conversations = array();
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$conversation = new Conversation($row);
			$conversation->get_members();
			$sql_last = "SELECT t1.message_id, t1.message_text, t2.username, t2.email, t3.number_of_messages
			FROM `conversation_messages` as t1
			LEFT JOIN `users` as t2 
			ON t2.user_id = t1.author_id
			INNER JOIN (
				SELECT conversation_id, COUNT(message_id) as `number_of_messages`
				FROM `conversation_messages`
				WHERE conversation_id = '" . $row['conversation_id'] . "'
				AND type != '" . Message::NOTIFICATION_TYPE ."'
			) as t3
			WHERE t1.conversation_id = '" . $row['conversation_id'] . "'
			AND t1.type != '" . Message::NOTIFICATION_TYPE ."'
			ORDER BY t1.date_posted DESC 
			LIMIT 1";
			$result_get_last = $mysqli->query($sql_last)
			or die ($mysqli->error);
			//echo $sql_last;
			if ($result_get_last->num_rows == 1) {
				//echo $sql_last;
				$row_message = mysqli_fetch_array($result_get_last, MYSQLI_ASSOC);
				$row_message['author'] = new ProfileUser($row_message);
				$message = new Message($row_message);
				$conversation->messages = array($message);
				$conversation->number_of_messages = $row_message['number_of_messages'];
			}
			else {
				$conversation->messages = array();
			}
			$conversations[] = $conversation;
		}
		http_response_code(200);
		echo json_encode($conversations, JSON_PRETTY_PRINT);
	}
	else {
		throw new UnexpectedValueException('UnexpectedValueException occured on request, no user is logged in');
	}
}
catch (UnexpectedValueException $e) {
	http_response_code(400);
	Database::print_exception($e);
}