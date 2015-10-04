<?php

class ConversationFactory {
	/*
	Helper class reponsible for creating conversation
	 */
	
	private static function _generate_header($reason) {
		/*
		(Mixed) -> String
		Generates the conversation header based on the reason, assumes correct input parameters
		 */
		$class_name = get_class($reason);
		switch ($class_name) {
			case 'PublicProposedDate':
			case 'GroupProposedDate':
				$header = "Conversation started on " . date('F j, Y') . " by " . $reason->creator->email . " for creating badminton event set for " . date('F j, Y', strtotime($reason->begin_datetime));
				break;
			default:
				throw new UnexpectedValueException('UnexpectedValueException occured on private method call _generate_header');
		}
		return $header;
	}
	
	public static function generate_conversation($reason) {
		/*
		(Mixed) -> Bool
		Attempts to create a conversation due to some reason
		 */
		if (is_subclass_of($reason, 'ProposedDate')) {
			if ((is_a($reason->creator, 'User') && ($reason->creator->user_id))) {
				$mysqli = Database::connection();
				$header = ConversationFactory::_generate_header($reason);
				$sql = "INSERT INTO `conversations` (conversation_name, date_started, date_recent_activity, header, date_id) VALUES ('" . date('F j, Y', strtotime($reason->begin_datetime)) . " - " . $reason->datename . "', NOW(), NOW(), '$header', '$reason->date_id')";
				$result = $mysqli->query($sql)
				or die ($mysqli->error);
			//echo $sql;
				$conversation_id = $mysqli->insert_id;
				//Add the user into the conversation
				$creator_id = $reason->creator->user_id;
				$sql = "INSERT INTO `conversation_members` (user_id, conversation_id, admin, date) VALUES('$creator_id', '$conversation_id', '" . Conversation::ADMIN_TYPE . "', NOW())";
				$result = $mysqli->query($sql)
				or die ($mysqli->error);
				return true;
			}
			else {
				throw new UnexpectedValueException('UnexpectedValueException occured on creating the conversation for the date');
			}
		}
		else {
			throw new UnexpectedValueException('Date is not a proposed type');
		}
	}
	
}
class Conversation {
	private $dbc;
	public $conversation_id, $conversation_name, $date_started, $date_recent_activity, $header;

	const ADMIN_TYPE = 1;
	const NONADMIN_TYPE = 0;

	public static $defaults = array(
		'conversation_id' => null,
		'conversation_name' => null,
		'date_started' => null,
		'date_recent_activity' => null,
		'members' => array(),
		'messages' => array(),
		'header' => null,
		'joinable' => 1,
		'chat_visible' => 1,
		'closed' => 0
	);

	private static $non_admin_permissions = array(
	);

	private static $admin_permissions = array(
		'accept_user_request_to_join_conversation'
	);

	private static $joinable_permissions = array(
		'join_conversation'
	);

	private static $unjoinable_permissions = array();

	private static $chat_visible_permissions = array();

	private static $chat_invisible_permissions = array();

	public function __construct(array $args = array()) {
		$defaults = self::$defaults;
		$args = array_merge($defaults, $args);
		$this->dbc = Database::connection();
		$this->conversation_id = (is_numeric($args['conversation_id'])) ? $args['conversation_id'] : null;
		$this->conversation_name = $args['conversation_name'];
		$this->date_started = $args['date_started'];
		$this->date_recent_activity = $args['date_recent_activity'];
		$this->members = (is_array($args['members'])) ? $args['members'] : array();
		$this->number_of_members - count($this->members);
		$this->messages = (is_array($args['messages'])) ? $args['messages'] : array();
		$this->number_of_messages = count($this->messages);
		$this->header = $args['header'];
		$this->joinable = (($args['joinable'] == 1)) ? true : false;
		if ($this->joinable) {
			$this->join_permission = self::$joinable_permissions;
		}
		else {
			$this->join_permission = self::$unjoinable_permissions;
		}
		$this->chat_visible = ($args['chat_visible'] == 1) ? true: false;
		if ($this->chat_visible) {
			$this->visiblilty_permissions = self::$chat_visible_permissions;
		}
		else {
			$this->visiblilty_permissions = self::$chat_invisible_permissions;
		}
		$this->everyone_admin = ($args['everyone_admin'] == 1) ? true: false;
		if (!$this->everyone_admin) {
			$this->member_priveleges = self::$non_admin_permissions;
		}
		else {
			$this->member_priveleges = self::$admin_permissions;
		}
		$this->badminton_date = (is_a($args['badminton_date'], 'BadmintonDate')) ? $args['badminton_date'] : null;
		if ($args['closed'] == 1) {
			$this->closed = true;
		}
		else {
			$this->closed = false;
		}
	}

	public function close_self() {
		/*
		(Null) -> Bool
		Closes self in the database
		*/
		if ($this->conversation_id) {
			$sql = "UDPATE `conversations` SET closed = '" . self::CLOSED . "' WHERE conversation_id = '$this->conversation_id'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			return true;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call close_self, the conversation id is not valid');
		}
	}

	public function open_self() {
		/*
		(Null) -> Bool
		Attempts to re-open the conversation in the database
		*/
		if ($this->conversation_id) {
			$sql = "UPDATE `conversations` SET closed = '" . Conversation::OPEN . "' WHERE conversation_id = '$this->conversation_id'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			return true;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call' . __METHOD__ . ' because the conversation id is invalid');
		}
	}

	public function user_pending(User $user) {
		/*
		(User) -> Bool
		Checks if the current user is pending to enter the conversation
		 */
		if ($this->conversation_id && $user->user_id) {
			$sql = "SELECT user_id FROM `conversation_requests` WHERE user_id = '$user->user_id' AND conversation_id = '$this->conversation_id'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			return $result->num_rows == 1;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call user_pending');
		}
	}

	public function action_allowed(User $user, $action) {
		/*
		(User, String) -> Bool
		Attempts to see if the action allowed is ok by the current user
		 */
		if ($this->user_admin($user)) {
			return true;
		}
		else {
 			return (in_array($action, array_merge($this->join_permission, $this->visiblilty_permissions, $this->member_priveleges)));
 		}
	}

	public function user_admin(User $user) {
		/*
		(User) -> Bool
		Attempts to check if the given user is an admin in the current conversation
		 */
		if ($this->conversation_id && $user->user_id) {
			$sql = "SELECT user_id FROM `conversation_members` WHERE conversation_id = '$this->conversation_id' AND admin = '" . self::ADMIN_TYPE . "'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			return $result->num_rows == 1;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call user_admin');
		}
	}

	public function get_members() {
		/*
		(Null) -> Null
		Creates the members properities based on the supplied conversation id
		 */
		if ($this->conversation_id) {
			$sql = "SELECT t1.user_id, t2.username, t2.avatar, t2.reputation, t2.email
			FROM `conversation_members` as t1 
			INNER JOIN users as t2 
			ON t2.user_id = t1.user_id
			WHERE conversation_id = '$this->conversation_id'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			$this->members = array();
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$member = new ProfileUser($row);
				$this->members[] = $member;
			}
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call ge_members, the conversation id is invalid');
		}
	}

	public function get_messages() {
		/*
		(Null) -> Null
		Attempts to fetch the messages in this conversation
		 */
		if ($this->conversation_id) {
			$sql = "SELECT t1.message_id, t1.message_text, t1.date_posted, t1.author_id as `user_id`, t1.type, t2.username, t2.email, t2.reputation, t2.avatar, t2.avatar_link
			FROM `conversation_messages` as t1 
			LEFT JOIN `users` as t2 
			ON t2.user_id = t1.author_id
			WHERE t1.conversation_id = '$this->conversation_id'
			ORDER BY t1.date_posted DESC";
			//echo $sql;
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			$this->messages = array();
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				//print_r($row);
				$row['author'] = new ProfileUser($row);
				//var_dump($row['author']);
				$message = new Message($row);
				$this->messages[] = $message;
			}
		}
	}

	public function update_conversation() {
		/*
		(Null) -> Bool
		Attempts to update the current conversation
		 */
		if ($this->conversation_id) {
			$sql = "UPDATE `conversations` SET date_recent_activity = NOW() WHERE conversation_id = '$this->conversation_id'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			return true;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call update_conversation');
		}
	}
}


class Message {
	/*
A generic message in the conversation
	 */
	
	private $dbc;
	public $message_id, $message_text, $author, $date_posted, $conversation;

	const MESSAGE_TYPE = 0;
	const NOTIFICATION_TYPE = 1;

	public static $defaults = array(
		'message_id' => null,
		'message_text' => null,
		'author' => null,
		'date_posted' => null,
		'conversation' => null,
		'parent' => null
	);

	public function __construct(array $args = array()) {
		$defaults = self::$defaults;
		$args = array_merge($defaults, $args);

		$this->dbc = Database::connection();
		$this->message_id =(is_numeric($args['message_id'])) ? $args['message_id'] : null;
		$this->message_text = $args['message_text'];
		$this->author = (is_a($args['author'], 'User')) ? $args['author'] : null;
		$this->date_posted = $args['date_posted'];
		$this->conversation = (is_a($args['conversation'], 'Conversation')) ? $args['conversation'] : null;
		$this->parent = (is_a($args['parent'], 'Message')) ? $args['parent'] : null;
	}

	public function get_author() {
		/*
		(Null) -> Null
		Attempts to create the author property
		 */
		if ($this->message_id) {
			$sql = "SELECT author_id FROM conversation_messages WHERE message_id = '$this->message_id'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			if ($result->num_rows == 1) {
				$author_id = mysqli_fetch_row($result)[0];
				$user = new ProfileUser(array(
					'user_id' => $author_id)
				);
				$this->author = $user;
			}
			else {
				throw new OutOfRangeException('OutOfRangeException on method call get_author. Tried to get the author of a message that does not exist in the database');
			}
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call get_author. Tried to get the author but no message id was given');
		}
	}

	public function get_conversation() {
		/*
		(Null) -> Null
		Will attempt to fetch the conversation object of the message
		 */
		if ($this->message_id) {
			$sql = "SELECT conversation_id FROM `conversation_messages` WHERE message_id = '$this->message_id'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			if ($result->num_rows == 1) {
				$this->conversation = new Conversation(array(
					'conversation_id' => $result->fetch_rows()[0])
				);
			}
			else {
				throw new OutOfRangeException('OutOfRangeException occured on method call get_conversation, the message does not exist');
			}
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call get_conversation');
		}
	}

	public function is_reply() {
		/*
		(Null) -> Null
		Checks if the current message is a reply or not
		 */
		if ($this->message_id) {
			$sql = "SELECT parent_id FROM `conversation_messages` WHERE message_id = '$this->message_id'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			$parent_id = $result->fetch_row()[0];
			return (!is_null($parent_id));
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call is_reply');
		}
	}

	public function insert_reply(Message $message) {
		/*
		(Message) -> Message
		Attempts to insert a reply into the current message
		 */
		if ($this->message_id && $message->message_text && $message->author) {
			$sql = "INSERT INTO `conversation_messages` (message_text, author_id, parent_id, date_posted) VALUES ('$message->message_text', '$message->author->user_id', '$this->message_id', NOW())";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			$id = $this->dbc->insert_id;
			$message->message_id = $id;
			$message->parent = clone $this;
			$message->date_posted = $this->dbc->query("SELECT NOW()")->fetch_row()[0];
			return $message;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on request from method call insert_reply');
		}
	}
}
?>