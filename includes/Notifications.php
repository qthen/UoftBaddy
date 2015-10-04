<?php
class NotificationFactory {
	/*
	Helper class to generate notifications
	 */	
	
	const UNREAD = 0;
	const READ = 1;

	public static $notification_type_contract = array(
		'JoinDiscussion' => 1,
		'ApproveJoinRequest' => 2,
		'JoinBadmintonDate' => 3,
		'ProposeBadmintonDate' => 4,
		'LeaveBadmintonDate' => 5,
		'WithdrawAbsence' => 6
	);
}


class Notification {
	/*
	Some sort of notification in the database that has not been read
	*/
	public $a_href, $message, $date_notified, $read_status, $notification_id;

	public static $defaults = array(
		'notification_id' => null,
		'a_href' => null,
		'message' => null,
		'date_notified' => null,
		'read_status' => 1);

	public function __construct(array $args = array()) {
		$defaults = self::$defaults;
		$args = array_merge($defaults, $args);

		$this->notification_id = $args['notification_id'];
		$this->read_status = $args['read_status'];
		$this->message = $args['message'];
		$this->date_notified = $args['date_notified'];
		$this->a_href = $args['a_href'];
	}
}


class NotificationPusher {
	/*
	Helper class to push notifications 
	 */
	
	public static $notification_messages = array(
		'JoinDiscussion' => '%s has requested to join this conversation "%s"',
		'ApproveJoinRequest' => '%s has approved %s request to join the discussion',
		'JoinBadmintonDate' => '%s has joined your badminton date "%s"',
		'LeaveBadmintonDate' => '%s can no longer attend your badminton date "%s"',
		'PostedCommentOnThread' => '%s has posted a comment in a thread you are in',
		'WithdrawAbsence' => '%s has withdrawn their absence and can now attend your badminton date %s'
	);
	
	public static function push_notification(Action $action) {
		$class = get_class($action);
		switch ($class) {
			case 'JoinDiscussion':
			case 'ApproveJoinRequest':
				ConversationNotificationPusher::push_notification($action);
				break;
			case 'JoinBadmintonDate':
			case 'LeaveBadmintonDate':
			case 'WithdrawAbsence':
				BadmintonDateNotificationPusher::push_notification($action);
				break;
			case 'PostedCommentOnThread':
			case 'PostedThread':
				ThreadNotificationPusher::push_notification($action);
				break;
			default:
				throw new UnexpectedValueException("$class is not a valid action class, tried to push in NotificaionPusher");
		}
	}
}


class ThreadNotificationPusher {
	/*
	Class for pushing notifications related to hreads
	 */
	public static function push_notification(Action $action) {
		$mysqli = Database::connection();
		$class = get_class($action);
		switch ($class) {
			case 'PostedCommentOnThread':
				$message = sprintf(NotificationPusher::$notification_messages[$class], $action->commenter->username);
				$a_href = 'therad.php?id=' . $action->thread->thread_id;
				list($message, $a_href) = Database::sanitize(array($message, $a_href));
				$participants = $action->thread->get_all_participants();
				foreach ($participants as $participant) {
					if ($participant->user_id != $action->commenter->user_id) {
						//No need to notify the commenter...
						$insert = "INSERT INTO `notifications` (message, user_id, type, a_href, read_status, date_notified) VALUES ('$message', '$participant->user_id', '" . NotificationFactory::$notification_type_contract[$class] . "', '$a_href', '" . NotificationFactory::UNREAD . "', NOW())";
						$result = $mysqli->query($insert)
						or die ($mysqli->error);
					}
				}
				return true;
				break;
			default:
				throw new OutOfRangeException('OutOfRangeException occured on pushing notification in ThreadNotificationPusher');
		}
	}
}


class BadmintonDateNotificationPusher {
	/*
	Class for pushing notifications related to badminton dates
	 */
	
	public static function push_notification(Action $action) {
		$mysqli = Database::connection();
		//var_dump($mysqli);
		$class = get_class($action);
		switch ($class) {
			case 'JoinBadmintonDate':
				$action->badminton_date->get_datename();
				$message = sprintf(NotificationPusher::$notification_messages[$class], $action->joiner->username, $action->badminton_date->datename);
				$a_href = $action->badminton_date->date_id;
				list($message, $a_href) = Database::sanitize(array($message, $a_href));
				$action->badminton_date->get_attendees(array(
					$action->joiner)
				);
				//print_r($action->badminton_date->attendees);
				foreach ($action->badminton_date->attendees as $attendant) {
					$a_href = "date.php?id=$a_href";
					$insert = "INSERT INTO `notifications` (message, user_id, type, a_href, read_status, date_notified) VALUES ('$message', '$attendant->user_id', '" . NotificationFactory::$notification_type_contract[$class] . "', '$a_href', '" . NotificationFactory::UNREAD . "', NOW())";
					$result = $mysqli->query($insert)
					or die ($mysqli->error);
				}
				return true;
				break;
			case 'LeaveBadmintonDate':
				$message = sprintf(NotificationPusher::$notification_messages[$class], $action->leaver->username, $action->badminton_date->datename);
				$a_href = $action->badminton_date->date_id;
				list($message, $a_href) = Database::sanitize(array($message, $a_href));
				$action->badminton_date->get_attendees();
				foreach ($action->badminton_date->attendees as $attendant) {
					if ($attendant->user_id != $action->leaver->user_id) {
						$a_href = "date.php?id=$a_href";
						$insert = "INSERT INTO `notifications` (message, user_id, type, a_href, read_status, date_notified) VALUES ('$message', '$attendant->user_id', '" . NotificationFactory::$notification_type_contract[$class] . "', '$a_href', '" . NotificationFactory::UNREAD . "', NOW())";
						$result = $mysqli->query($insert)
						or die ($mysqli->error);
					}
				}
				return true;
				break;
			case 'WithdrawAbsence':
				$message = sprintf(NotificationPusher::$notification_messages[$class], $action->withdrawer->username, $action->badminton_date->datename);
				$a_href = $action->badminton_date->date_id;
				list($message, $a_href) = Database::sanitize(array($message, $a_href));
				$action->badminton_date->get_attendees();
				foreach ($action->badminton_date->attendees as $attendant) {
					if ($attendant->user_id != $action->withdrawer->user_id) {
						$a_href = "date.php?id=$a_href";
						$insert = "INSERT INTO `notifications` (message, user_id, type, a_href, read_status, date_notified) VALUES ('$message', '$attendant->user_id', '" . NotificationFactory::$notification_type_contract[$class] . "', '$a_href', '" . NotificationFactory::UNREAD . "', NOW())";
						$result = $mysqli->query($insert)
						or die ($mysqli->error);
					}
				}
				return true;
				break;
			default:
				throw new UnexpectedValueException("$class is not a valid action class, tried to push in BadmintonDateNotificationPusher");
		}
	}
}


class ConversationNotificationPusher {
	/*
	Pushes notifcation into the conversation
	 */
	
	public static function push_notification(MessageAction $action) {
		$class = get_class($action);
		$dbc = Database::connection();
		switch ($class) {
			case 'JoinDiscussion':
				$message = sprintf(NotificationPusher::$notification_messages[$class], $action->joiner->email, $action->conversation->conversation_name);
				$sql = "INSERT INTO `conversation_messages` (conversation_id, type, message_text) VALUES ('$action->conversation->conversation_id', '" . Conversation::NOTIFICATION_TYPE . "', '$message')";
				$result = $dbc->query($sql)
				or die ($dbc->error);
				return true;
				break;
			case 'ApproveJoinRequest':
				$message = sprintf(NotificationPusher::$notification_messages[$class], $action->approver->email, $action->joiner->email);
				$sql = "INSERT INTO `conversation_messages` (conversation_id, type, message_text) VALUES ('$action->conversation->conversation_id', '" . Conversation::NOTIFICATION_TYPE . "', '$message')";
				$result = $dbc->query($sql)
				or die ($dbc->error);
				return true;
				break;
			default:
				throw new UnexpectedValueException("$class is not a valid action, tried to push in ConversationNoticationPusher");
		}
	}
}


class GeneralCommentNotificationPusher {
	const GENERAL_COMMENT_NOTIFICATION_TYPE = 2;

}
?>