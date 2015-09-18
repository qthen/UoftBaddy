<?php
/*
Think about if users can report if certian blocks have already been booked, either through annotation or by selecting, but try to think how to avoid trolls

Maybe higher points for having succesful meetups?
 */
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
/*require_once __DIR__ . '/Database.php';*/
require_once __DIR__ . '/../vars/constants.php';


class User {
	/*
	A class for some abstract user
	 */

	private $dbc;	
	public $user_id, $username, $points, $avatar, $description, $missed_ratio, $level, $endorses;

	const JOINED_STATUS = 1;
	const LEFT_STATUS = 0;
	const ABSENCE_STATUS = 2;
	const AVATAR_DIR = 'uploads/';
	const CONFIRMED = 1;
	const UNCONFIRMED = 0; //Unconfirmed user

	public static $level_contracts = array(
		1 => 'Beginner',
		2 => 'Intermediate',
		3 => 'Advanced',
		4 => 'Competitive',
		5 => 'Ranked',
		6 => 'Not disclosed'
	);

	public static $defaults = array(
		'user_id' => null,
		'username' => null,
		'avatar' => 'default.png',
		'email' => null,
		'description' => null,
		'reputation' => 0,
		'date_registered' => null,
		'last_seen' => null,
		'commuter' => 2,
		'level' => 1,
		'program' => 'Unspecified',
		'avatar_link' => null,
		'bio' => null
	);

	public function __construct(array $args = array()) {
		$this->dbc = Database::connection();
		$defaults = self::$defaults;
		$args = array_merge($defaults, $args);

		//Assign the object properites
		$this->user_id = (is_numeric($args['user_id'])) ? $args['user_id'] : null;
		$this->username = $args['username'];
		if (!$args['avatar']) {
			$this->avatar = self::AVATAR_DIR . 'default.png';
		}
		else {
			$this->avatar = self::AVATAR_DIR . $args['avatar'];
		}
		$this->email = $args['email'];
		$this->description = $args['description'];
		$this->reputation = $args['reputation'];
		$this->date_registered = $args['date_registered'];
		$this->last_seen = $args['last_seen'];
		if ($args['commuter'] == 1) {
			$this->commuter = true;
		}
		else if ($args['commuter']  == 2) {
			$this->commuter = 2; //Undisclosed
		}
		else {
			$this->commuter = false;
		}
		if (in_array($args['level'], self::$level_contracts)) {
			$this->level = self::$levels_contracts[$args['level']];
		}
		else {
			$this->level = 'Not disclosed';
		}
		$this->program = $args['program'];
		$this->avatar_link = $args['avatar_link'];
		$this->bio = $args['bio'];
	}

	public function get_fields() {
		/*
		(Null) -> Null
		Gets user email
		 */
		if ($this->user_id) {
			$sql = "SELECT email, username FROM users WHERE user_id = '$this->user_id'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			list($this->email, $this->username) = mysqli_fetch_array($result, MYSQLI_NUM);
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call get_fields');
		}
	}

	public static function generate_token() {
		/*
		(Null) -> Null
		Generates a random token converted into hex
		 */	
		$size = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB);
		$new_token = bin2hex(mcrypt_create_iv($size, MCRYPT_DEV_RANDOM));
		return $new_token;
	}

	public static function email_exists($email) {
		/*
		(Str) -> Bool
		Checks to see if an email exists in the database
		 */
		if ($email) {
			$mysqli = Database::connection();
			$email = Database::sanitize($email);
			$sql = "SELECT user_id FROM users WHERE email = '$email' AND type != 0";
			$result = $mysqli->query($sql)
			or die ($mysqli->error);
			return ($result->num_rows == 1);
		}
		else {
			return false;
		}
	}

	public function get_number_of_joins() {
		/*
		(Null) -> Null
		Attempts to create the property number_of_joins
		*/
		if ($this->user_id) {
			$sql = "SELECT COUNT(*)
			FROM `joins`
			WHERE user_id = '$this->user_id'
			AND status = '" . self::JOINED_STATUS . "'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			$this->number_of_joins = $result->fetch_row()[0];
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call get_number_of_joins');
		}
	}

	public function get_number_of_leaves() {
		/*
		(Null) -> Null
		Attempts to create the propery number_of_leaves
		*/
		if ($this->user_id) {
			$sql = "SELECT COUNT(*)
			FROM `joins`
			WHERE user_id = '$this->user_id'
			AND status = '" . self::LEFT_STATUS . "'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			$this->number_of_leaves = $result->fetch_row()[0];
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call get_number_of_joins');
		}
	}

	public function get_number_of_hosted_events() {
		if ($this->user_id) {
			$sql = "SELECT COUNT(*)
			FROM `badminton_dates`
			WHERE creator_id = '$this->user_id'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			$this->number_of_hosted_events = $result->fetch_row()[0];
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call get_number_of_hosted_events because the user_id is invalid');
		}
	}

	public function is_confirmed() {
		/*
		(Null) -> Bool
		Checks to see if the currently logged in user has a confirmed University of Toronto email address
		*/
		if ($this->user_id) {
			$sql = "SELECT type FROM `users` WHERE user_id = '$this->user_id' AND type = '" . self::UNCONFIRMED . "'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			return $result->num_rows == 0;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call is_confirmed because the user id is invalid');
		}
	}

	public function user_exists() {
		/*
		(Null) -> Bool
		Checks to see if this user's user_id exists in the database
		*/
		if ($this->user_id) {
			$sql = "SELECT user_id FROM `users` WHERE user_id = '$this->user_id'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			return ($result->num_rows == 1);
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call user_exists because the user id is invalid');
		}
	}

	public function join_conversation(Conversation $conversation) {
		/*
		(Conversation) -> User
		Attempts to join the supplied conversation
		*/
		if ($this->user_id && $conversation->conversation_id) {
			if (!$this->in_conversation($conversation)) {
				if ($conversation->action_allowed(clone $this, __METHOD__)) {
					$sql = "INSERT INTO `conversation_members` (user_id, conversation_id, date) VALUES ('$this->user_id', '$conversation->conversation_id', NOW())";
					$result = $this->dbc->query($sql)
					or die ($this->dbc->error);

					//Spawn notification here
					$action = new JoinDiscussion(array(
						'joiner' => clone $this,
						'conversation' => $conversation)
					);
					NotificationPusher::push_notification($action); //Not sure if make this succeed or die yet...
					return true;
				}
				else {
					$sql = "INSERT INTO `conversation_requests` (conversation_id, joiner_id, date_requested) VALUES ('$conversation->conversation_id', '$this->user_id', NOW())";
					$result = $this->dbc->query($sql)
					or die ($this->dbc->error);
					return true;
				}
			}
			else {
				return false;
			}
		}
	
	}

	public function in_conversation(Conversation $conversation) {
		/*
		(Conversation) -> Bool
		Given an conversation object, attempts to verify if this user is currently in the conversation
		 */
		if ($this->user_id && $conversation->conversation_id) {
			$sql = "SELECT user_id FROM `conversation_members` WHERE conversation_id = '$conversation->conversation_id'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			return $result->num_rows == 1;
		}
		else {
			return false;
		}
	}

	public static function get_current_user() {
		/*
		(Null) -> User
		Returns the current user object, if no user is logged in returns an anonymous user object
		 */
		try {
			$user_id = self::get_current_user_id();
			if (is_numeric($user_id)) {
				$mysqli = Database::connection();
				$sql = "SELECT username, email FROM users WHERE user_id = '$user_id'";
				$result = $mysqli->query($sql)
				or die ($mysqli->error);
				if ($result->num_rows == 1) {
					list($username, $email) = mysqli_fetch_row($result);
					$user = new CurrentUser(array(
						'user_id' => $user_id,
						'username' => $username,
						'email' => $email)
					);	
				}
				else {
					throw new UnexpectedValueException;
				}
			}
			else {
				throw new UnexpectedValueException;
			}
		}
		catch (UnexpectedValueException $e) {
			$user = new AnonymousUser;
		}
		finally {
			return $user;
		}
	}

	private static function get_current_user_id() {
		/*
		(Null) -> Int
		Fetches the current user id
		 */
		//print_r($_SESSION);
		$mysqli = Database::connection();
		if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
			//Check for a cookie
			if (!empty($_COOKIE['token']) && is_numeric($_COOKIE['user_id'])) {
				$token = $_COOKIE['token'];
				$user_id = $_COOKIE['user_id'];
				$sql = "SELECT token FROM users WHERE user_id = '$user_id'";
				$result = $mysqli->query($sql)
				or die($mysqli->error);
				if ($result->num_rows == 1) {
					//Attempt to verify the token
					$token_hashed = mysqli_fetch_row($result)[0];
					$verify = password_verify($token, $token_hashed);
					if ($verify) {
						//Create the session again
						$_SESSION['user_id'] = $user_id;
						return $user_id;
					}
					else {
						return 'None';
					}
				}
				else {
					return 'None';
				}
			}
			else {
				return 'None';
			}
		}
		else {
			return $_SESSION['user_id'];
		}
	}

	public function get_activity() {
		/*
		(Null) -> Null
		Attempts to get the user activity
		 */
		return ActionFactory::fetch_activity(clone $this);

	}
}


class ProfileUser extends User {
	/*
	Class for a user's profile whom is not currently logged in but in display
	 */
}

class CurrentUser extends User {
	/*
	Class for the current user that is using the website and is logged in
	 */
	
	public function __construct($args) {
		parent::__construct($args);

		//Now assign the database connection
		$this->dbc = Database::connection();
	}

	public function edit_self(ProfileUser $editted_profile) {
		/*
		(ProfileUser) -> CurrentUser
		Attempts to edit the current user's profile based on the new specifications, assumes sanitized variables
		 */
		if ($this->user_id) {
			$sql = "UPDATE `users` SET program = '$editted_profile->program' AND level = '$editted_profile->level' AND commuter = '$editted_profile->commuter'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
		}
	}

	public function edit_avatar() {
		throw new BadMethodCallException('Not yet implemented');
	}

	public function post_message(Conversation $conversation, Message $message) {
		/*
		(Conversation, Message) -> Mixed(Message/Bool)
		Given a conversation, treats it as a stack and pushes a message on the end. Messaage does not have to be created yet.
		Assumes santizied input
		Returns the posted message in the database
		 */
		if ($this->user_id && $conversation->conversation_id && $message->message_text && $this->in_conversation($conversation)) {
			$sql = "INSERT INTO conversation_messages (conversation_id, author_id, message_text, date_posted, type) VALUES ('$conversation->conversation_id', '$this->user_id', '$message->message_text', NOW(), '" . Message::MESSAGE_TYPE . "')";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);

			$message_id = $this->dbc->insert_id;
			$sql_refetch = "SELECT message_text, date_posted FROM `conversation_messages` WHERE message_id = '$message_id'";
			$result = $this->dbc->query($sql_refetch)
			or die ($this->dbc->error);
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$row['message_id'] = $message_id;
			$row['author'] = clone $this;
			$message = new Message($row);

			//Update the conversation
			$conversation->update_conversation();
			return $message;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call post_message, incorrect input');
		}
	}

	public function post_reply(Message $message, Message $reply) {
		/*
		(Message, Message) -> Message
		Treats the first parameter as a stack and attempts to push the second parameter are a reply into the first parameter
		 */
		if ($this->user_id && $message->message_id && $reply->message_text) {
			$reply->author = clone $this;
			return $message->insert_reply($reply);
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call post_reply`');
		}
	}

	public function accept_user_request_to_join_conversation(Conversation $conversation, User $user) {
		/*
		(User) -> Bool
		Attempts to bring the user from `conversation_pending` into the conversation
		 */
		if ($this->user_id && $this->in_conversation($conversation)) {
			if ($conversation->action_allowed(clone $this, __METHOD__)) {
				if ($conversation->user_pending($user)) {
					if ($conversation->everyone_admin) {
						$sql = "INSERT INTO `conversation_members` (conversation_id, user_id, admin, date) VALUES ('$conversation->conversation_id', '$user->user_id', '" . Conversation::ADMIN_TYPE . "', NOW())";
						$result = $this->dbc->query($sql)
						or die ($this->dbc->error);
						return true;
					}
					else {
						$sql = "INSERT INTO `conversation_members` (conversation_id, user_id, admin, date) VALUES ('$conversation->conversation_id', '$user->user_id', '" . Conversation::NONADMIN_TYPE . "', NOW())";
						$result = $this->dbc->query($sql)
						or die ($this->dbc->error);
						return true;
					}
					//Push the notification into the conversation
					$action = new ApproveJoinRequest(array(
						'joiner' => $user,
						'approver' => $approver)
					);
					NotificationPusher::push_notification($action);
				}
				else {
					return true;
				}
			}
			else {
				throw new BadMethodCallException('BadMethodCallException occured on method call ' . __METHOD__ . ' as the user does not have permission');
			}
		}
	}

	public function delete_message(Message $message) {
		/*
		(Message) -> Bool
		 */
		if ($message->message_id) {
			$message->get_author();
			if ($this->user_id && $message->author->user_id == $this->user_id) {
				$sql = "DELETE FROM `conversation_messages` WHERE message_id = '$message->message_id'";
				$result = $this->dbc->query($sql)
				or die ($this->dbc->error);
				return true;
			}
			else {
				throw new OutOfBoundsException('You do not have permission to preform this action');
				return false;
			}
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call delete_message');
		}
	}

	public function log_login() {
		/*
		Updates the last time the user logged in
		 */
		if ($this->user_id) {
			$sql = "UPDATE users SET last_seen = NOW() WHERE user_id = '$this->user_id' LIMIT 1";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			return true;
		}
		else {
			return false;
		}
	}

	final public function delete_profile_comment(ProfileComment $comment) {
		/*
		(ProfileComment) -> Bool
		Attempts to delete a profile comment on the user's profile
		 */
		if ($comment->comment_id && $comment->author->user_id == $this->user_id || $comment->profile->user_id == $this->user_id) {
			return $comment->self_destruct();
		}
		else {
			throw new OutOfBoundsException('OutOfBoundsException occured on request, you do not have permission to preform this action');
		}
	}

	final public function edit_thread(Thread $thread) {
		/*
		(Thread) -> Thread
		Attempts to edit the thread with the new thread
		 */
		if ($this->user_id) {
			$thread->get_author();
			if ($thread->author->user_id == $this->user_id) {
				$sql = "UPDATE `threads` SET thread_text = '$thread->thread_text' AND thread_title = '$thread->thread_title' WHERE thread_id = '$thread->thread_id' LIMIT 1";
				$result = $this->dbc->query($sql)
				or die ($this->dbc->error);
				return $thread;
			}
			else {
				throw new OutOfBoundsException('OutOfBoundsException occured on request, the currently logged in user does not have permission to perform this action');
			}
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call edit_thread because user it not valid');
		}
	}

	final public function post_thread(Thread $thread) {
		/*
		(Thread) -> Thread
		Attempts to post the comment into the database
		 */
		if ($this->user_id && $thread->thread_text && $thread->type) {
			if ($thread->type == 1) {
				$sql = "INSERT INTO `threads` (thread_text, author_id, date_posted, type, date_play) VALUES ('$thread->thread_text', '$this->user_id', NOW(), '$thread->type', '$thread->date_play')";
			}
			else {
				$sql = "INSERT INTO `threads` (thread_text, author_id, date_posted, type) VALUES ('$thread->thread_text', '$this->user_id', NOW(), '$thread->type')";
			}
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			$thread->thread_id = $this->dbc->insert_id;

			//Push the action
			$action = new PostedThread(array(
				'thread' => $thread,
				'poster' => clone $this)
			);
			ActionPusher::push_action($action);
			return $thread;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call post_thread');
		}
	}

	final public function post_thread_comment(Thread $thread, ThreadComment $comment) {
		/*
		(Thread, ThreadComment) -> ThreadComment
		Attempts to post a comment on a thread
		 */
		if ($this->user_id && $thread->thread_id && $comment->comment_text) {
			$sql = "INSERT INTO `thread_comments` (thread_id, comment_text, date_posted, author_id, parent_id) VALUES('$thread->thread_id', '$comment->comment_text', NOW(), '$this->user_id', null)";
			//echo $sql;
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			$id = $this->dbc->insert_id;
			$comment->comment_id = $id;
			

			//Create the action
			$action = new PostedCommentOnThread(array(
				'commenter' => clone $this,
				'thread' => $thread)
			);

			//Push the action
			ActionPusher::push_action($action);

			//Now push the notification
			NotificationPusher::push_notification($action);			
			return $comment;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call post_thread_comment');
		}
	}

	final public function post_comment_on_user_profile(User $user, ProfileComment $comment) {
		/*
		(User, ProfileComment) -> Mixed(ProfileComment/Bool)
		Posts a comment on a user's profile
		 */
		if ($this->user_id && $user->user_id) {
			$sql = "INSERT INTO profile_comments (comment_text, author_id, profile_id, date_posted) VALUES ('$comment->comment_text', '$this->user_id', '$user->user_id', NOW())";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			$comment->comment_id = $this->dbc->insert_id;
			$comment->date_posted = date('M j, y');

			$comment->author = clone $this;
			return $comment;
		}
		else {
			return false;
		}
	}

	final public function change_reputation(ReputationChange $change) {
		/*
		(ReputationChange) -> Bool
		Applies the reputation change to the current user
		 */
	}

	final private function log_reputation_change(ReputationChange $change) {
		/*
		(ReputationChange) -> Bool
		Logs the reputation change into the database
		 */
	}

	public function in_date(BadmintonDate $badminton_date) {
		/*
		(User) -> Bool
		Checks if the current user has joined the badminton date
		 */
		if ($badminton_date->date_id && $user->user_id) {
			$mysqli = Database::connection();
			$sql = "SELECT user_id FROM joins WHERE user_id = '$this->user_id' AND date_id = '$date->date_id'";
			$result = $mysqli->query($sql)
			or die ($mysqli->error);
			return ($result->num_rows == 1);
		}
		else {
			return false;
		}
	}

	public function in_group(Group $group) {
		/*
		(Group) -> Bool
		 */
		if ($this->user_id && $group->group_id) {
			$sql = "SELECT group_id FROM `group_members` WHERE group_id = '$group->group_id' AND user_id = '$this->user_id";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			if ($result->num_rows == 1) {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

	public function join_thread(Thread $thread, Array $array_of_conditionals) {
		/*
		(Thread) -> Int
		Attempts to join the thread
		*/
		if ($this->user_id && $thread->thread_id) {
			if (count($array_of_conditionals) == 2) {
				//Conditionals are satisifised, insert
				$sql = "INSERT INTO `thread_joins` (thread_id, user_id, date_joined, begin_conditional, end_conditional) VALUES ('$thread->thread_id', '$this->user_id', NOW(), '" . $array_of_conditionals['begin_conditional'] . "', '" . $array_of_conditionals['end_conditional'] . "')";
				$result = $this->dbc->query($sql)
				or die ($this->dbc->error);
				return $this->dbc->insert_id;
			}
			else {
				$sql = "INSERT INTO `thread_joins` (thread_id, user_id, date_joined) VALUES('$thread->thread_id', '$this->user_id', NOW())
				ON DUPLICATE KEY UPDATE join_id = join_id";
				$result = $this->dbc->query($sql)
				or die ($this->dbc->error);
				return $this->dbc->insert_id;
			}
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call join_thread');
		}
	}

	public function close_conversation(Conversation $conversation) {
		/*
		(Conversation) -> Bool
		*/
		if ($this->user_id) {
			if ($conversation->action_allowed($this, __METHOD__)) {
				$conversation->close_self();
			}
			else {
				throw new OutOfBoundsException('OutOfBoundsException occured on method call close_conversation becauase the current user does not have permission to perform this action');
			}
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call ' . __METHOD__ . ' because the user id is invalid');
		}
	}

	public function open_conversation(Conversation $conversation) {
		/*
		(Conversation) -> Bool
		*/
		if ($this->user_id) {
			if ($conversation->action_allowed($this, __METHOD__)) {
				$conversation->open_self();
			}
			else {
				throw new OutOfBoundsException('OutOfBoundsException occured on method call close_conversation becauase the current user does not have permission to perform this action');
			}
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call ' . __METHOD__ . ' because the user id is invalid');
		}
	}

	public function get_pending_actions() {
		/*
		(Null) -> Array
		*/
		if ($this->user_id) {
			$sql = "SELECT t1.token_id, t1.date_issued, t2.user_to_endorse, t2.date_id, t3.datename, t3.begin_datetime, t3.end_datetime
			FROM `pending_actions` as t1 
			LEFT JOIN `pending_user_endorsements` as t2 
			ON t2.token_id = t1.token_id
			LEFT JOIN `badminton_dates` as t3 
			ON t3.date_id = t2.date_id
			WHERE t1.date_issued <= NOW()
			AND t1.active = '" .PendingAction::ACTIVE . "'
			AND t1.awaiting_user = '$this->user_id'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			$actions = array();
			while ($row = mysqli_fetch_array($actions, MYSQLI_ASSOC)) {
				$row['user_to_endorse'] = new ProfileUser(array(
					'user_id' => $row['user_to_endorse'],
					'username' => $row['username'],
					'reputation' => $row['reputation'])
				);
				$row['cause_date'] = new BadmintonDate($row);
				$pending_action = new PendingEndorsementToken($row);
				array_push($actions, $pending_actions);
			}
			return $actions;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call ' . __METHOD__ . ' because the user id is invalid');
		}
	}

	final public function join_badminton_date(BadmintonDate $badminton_date) {
		/*
		(Date) -> Bool
		The current user attempts to join the badminton date
		 */
		if ($this->user_id && $badminton_date->date_id) {
			$sql = "INSERT INTO joins (date_id, user_id, date_joined, status) VALUES('$badminton_date->date_id', '$this->user_id', NOW(), '" . self::JOINED_STATUS . "')
			ON DUPLICATE KEY UPDATE join_id = join_id";
			//echo $sql;
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);

			$affected_rows = $this->dbc->affected_rows;
			if ($affected_rows == 1) {
				//New row was inserted
				//Push the notificatoins and check for method allowed
				$this->get_fields();
				$action = new JoinBadmintonDate(array(
					'joiner' => clone $this,
					'badminton_date' =>  $badminton_date,
					'trigger' => clone $this)
				);
				//Push action
				ActionPusher::push_action($action);
				//Push notification, only if the current user is not the creator of the badminton date
				//Push the pending action to evaluate the hoster, only if the current user is not the creator of the badminton date
				if ($this->user_id != $badminton_date->creator->user_id) {
					NotificationPusher::push_notification($action);
					$sql = "SELECT end_datetime FROM badminton_date WHERE date_id = '$badminton_date->date_id'";
					$result = $this->dbc->query($sql)
					or die ($this->dbc->error);
					if ($result->num_rows == 1){
						$end_datetime = mysqli_fetch_row($result)[0];
						//Issue the pending endorsement
						$sql = "INSERT INTO `pending_actions` (date_issued, awaiting_user, active) VALUES ('$end_datetime', '$this->user_id', '" . self::ACTIVE . "')";
						$result = $this->dbc->query($sql)
						or die ($this->dbc->error);

						$token_id = $this->dbc->insert_id;
						$badminton_date->get_creator();
						$sql = "INSERT INTO `pending_user_endorsements` (token_id, user_to_endorse, date_id) VALUES ('$token_id', '{$badminton_date->creator->user_id}', '$badminton_date->date_id')";
						$result = $this->dbc->query($sql)
						or die ($this->dbc->error);
						return true;
					}
					else {
						throw new OutOfRangeException('OutOfRangeException occured on method call ' . __METHOD__ . ' because the date id does not exist');
					}
				}
				else {
					return true;
				}
			}
			return true;
		}
		else {
			return false;
		}
	}

	final public function cancel_date(BadmintonDate $badminton_date) {
		/*
		(Date) -> Bool
		If the current user is the creator, will attempt to cancel the badminton date, usually because unable to book or cannot go
		 */
		try {
			if ($badminton_date->date_id) {
				//If already confirmed and the user is the one who booked this court than 
				$sql = "UPDATE badminton_dates SET status = 0 WHERE date_id = '$badminton_date->date_id' LIMIT 1";
				$result = $this->dbc->query($sql)
				or die ($this->dbc);
				$this->log_action(); //Need to think about how to log actions
				return true;
 			}
 			else {
 				throw new UnexpectedValueException;
 			}

		}
		catch (UnexpectedValueException $e) {
			return false;
		}
	}

	final public function log_action(Action $action) {
		/*
		(Action) -> Bool
		With a given action, logs it into the user actions database
		 */
		throw new BadMethodCallException; //Not yet implemented
	}

	final public function report_fake_date(ConfirmedDate $badminton_date) {
		/*
		(ConfirmedDate) -> Bool
		Report the date as faked, no courts were actually booked or know for sure it is fake.
		 */
		try { 
			if ($badminton_date->date_id) {
				$sql = "INSERT INTO reports (date_id, repoter_id) VALUES ('$badminton_date->date_id', '$this->user_id')";
				$result = $this->dbc->query($sql)
				or die ($this->dbc->error);
				return true;
			}
			else {
				throw new UnexpectedValueException;
			}
		}
		catch (UnexpectedValueException $e) {
			return false;
		}
	}

	final public function get_notifications() {
		/*
		(Null) -> Array of Notifiations
		Attempts to get the notifications for this user
		 */
		if ($this->user_id) {
			$sql = "SELECT notification_id, message, user_id, type, a_href, read_status, date_notified
			FROM `notifications`
			WHERE read_status = '" . NotificationFactory::UNREAD . "'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			$notifications = array();
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$notification = '<a href="' . $row['a_href'] . '">' . $row['message'] . '</a>';
				array_push($notifications, $notification);
			}
			return $notifications;
		}
	}

	final public function leave_date(BadmintonDate $badminton_date) {
		/*
		(BadmintonDate) -> Bool
		Attempts for the current user to leave the badminton date
		 */
		if ($this->user_id && $badminton_date->date_id) {
			if ($this->in_date($badminton_date)) {
				if ($badminton_date->can_be_left()) {
					$sql = "UPDATE `joins` SET status = '" . self::LEFT_STATUS . "' WHERE date_id = '$badminton_date->date_id' AND user_id = '$this->user_id'";
					$result = $this->dbc->query($sql)
					or die ($this->dbc->error);
					$action = new LeaveBadmintonDate(array(
						'leaver' => clone $this,
						'badminton_date' => $badminton_date)
					);

					$affected_rows = $this->dbc->affected_rows;

					if ($affected_rows == 1) {
						//New row was inserted
						//Push the action
						ActionFactory::push_action($action);

						//Push notifications to the group
						NotificationFactory::push_notification($action);			
					}
					return true;
				}
				else {
					return false;
				}
			}
			else {
				return true;
			}
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on request on method call leave_date, invalid parameters');
		}
	}

	final public function notify_absence(BadmintonDate $badminton_date) {
		/*
		Think about this function
		 */
		if ($badminton_date->date_id && $this->user_id && $badminton->datetime) {
			if ($this->user_joined($badminton_date) && (strtotime($badminton_date->datetime) > time())) {
				$mysqli = Database::connection();
				$sql = "UPDATE joins SET status = '" . self::ABSENCE_STATUS ."' WHERE date_id = '$badminton_date->date_id' AND user_id = '$this->user_id'";
				$result = $mysqli->query($sql)
				or die ($mysqli->error);
				$user->log_action();
				return true;
			}
			else {
				throw new OutOfRangeException('OutOfRangeException occured on method call, user has not joined this badminton date and marking as absent is irrelevant');
			}
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call, likely malformed date input or invalid user id');
			return false;
		}
	}

	

	public function create_group(Group $group_to_be_created) {
		/*
		(Group) -> Bool
		Attempts to create a group with the given parameters
		 */
		if ($group_to_be_created->group_name) {
			$sql = "INSERT INTO groups (group_name, group_description) VALUES ('$group_to_be_created->group_name', '$group_to_be_created->group_description')";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			return true;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured during method call. Group name likely invalid');
			return false;
		}
	}

	public function mark_user_as_absent(User $user, ConfirmedDate $badminton_date) {
		/*
		(User) -> Bool
		Marks a user as absent from an event
		 */
		if ($user->user_id && $badminton_date->badminton_date && (strtotime($badminton->datetime) > time())) {
			$mysqli = Database::connection();
			$sql = "INSERT INTO attendance (date_id, user_id, trigger_id, status) VALUES('$badminton_date->date_id', '$user->user_id', '$this->user_id', 0)";
			$result = $mysqli->query($sql)
			or die ($mysqli->error);
			return true;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured while calling method `mark_user_as_absent`');
			return false;
		}
	}

	public function mark_user_as_present(User $user, ConfirmedDate $badminton_date) {
		/*
		(User, ConfirmedDate) -> Bool
		Marks a user as present for a confirmed badminton date that has already passed
		 */
		if ($user->user_id && $badminton_date->badminton_date && (strtotime($badminton->datetime) > time())) {
			$mysqli = Database::connection();
			$sql = "INSERT INTO attendance (date_id, user_id, trigger_id, status) VALUES('$badminton_date->date_id', '$user->user_id', '$this->user_id', 0)";
			$result = $mysqli->query($sql)
			or die ($mysqli->error);
			return true;
		}
		else {
			return false;
		}	
	}

	public function endorse_user(User $user_to_endorse, PendingEndorsementToken $token) {
		/*
		(User) -> Bool
		The purpose of this function is to give the user some reputation points in order to show that this user comes and shows regularly or maybe plays well
		 */
		if ($this->user_id && $user->user_id) {
			if ($token->is_valid()) {
				$sql = "UPDATE `users` SET reputation = ";
				$sql .= ($token->reputation_change >=0) ? ' reputation + ' . $token->reputation_change : ' reputation - ' . abs($token->reputation_change);
				$sql .= " WHERE user_id = '{$token->user_to_endorse->user_id}' LIMIT 1";
				$result = $this->dbc->query($sql)
				or die ($this->dbc->error);

				//Destroy the token
				$token->destroy_self();

				if ($token->reputation_change >= 0) {
					$sql = "INSERT INTO `user_feedback` (user_id, status) VALUES ('{$token->user_to_endorse->user_id}', 1)";
				}
				else {
					$sql = "INSERT INTO `user_feedback` (user_id, status) VALUES ('$token->user_to_endorse->user_id}', 0)";
				}
				$result = $this->dbc->query($sql)
				or die ($this->dbc->error);
				return true;
			}
			else {
				throw new OutOfBoundsException('OutOfBoundsException occured on request, the token id is expired');
			}
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call  ' . __METHOD__ . ' because the token_id is invalid');
		}
	}

	public function claim_unavailable_date(UnavailableDate $badminton_date) {

	}

	public function propose_badminton_date(ProposedDate $badminton_date) {
		/*
		(Children of ProposedDate) -> BadmintonDate
		Attempts to create a new badminton date into the database, assumes sanitized input already
		 */
		if ((strtotime($badminton_date->begin_datetime) > time()) && (strtotime($badminton_date->end_datetime) > time())) {
			$sql = "INSERT INTO badminton_dates (datename, begin_datetime, end_datetime, creator_id, confirmed, summary) VALUES ('$badminton_date->datename', '$badminton_date->begin_datetime', '$badminton_date->end_datetime', '$this->user_id', '" . BadmintonDate::CONFIRMED . "', '$badminton_date->summary')
			ON DUPLICATE KEY UPDATE date_id = date_id";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			if (($this->dbc->affected_rows == 2) || ($this->dbc->affected_rows == 0)) {
				//Update was preformed
				$sql = "SELECT date_id FROM `badminton_dates` WHERE begin_datetime = '$badminton_date->begin_datetime' AND end_datetime = '$badminton_date->end_datetime'";
				$result = $this->dbc->query($sql)
				or die ($this->dbc->error);
				$badminton_date->date_id = mysqli_fetch_row($result)[0];
			}
			else {
				//Join the badminton date
				$badminton_date->date_id = $this->dbc->insert_id;
				//Push the action
				$action = new ProposeBadmintonDate(array(
					'proposer' => clone $this,
					'badminton_date' => $badminton_date)
				);
				ActionPusher::push_action($action);
				$this->join_badminton_date($badminton_date);
				//Create the badminton conversation
				ConversationFactory::generate_conversation($badminton_date);


			}
			return $badminton_date;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on request on the method call propose_badminton_date');
			return false;
		}
	}
}


class AnonymousUser extends User {
	/*
	Class for an anonymous user
	 */
	private $dbc;
	public $user_id, $username;

	public function __construct() {
		/*
		Construct an `anonmyous` user object
		 */
		$this->user_id = null;
		$this->username = 'Anonymous';
		$this->avatar = ANONYMOUS_AVATAR;
		$this->date_joined = '2015-8-7';
	}

	public function log_login() {
		/*
		Updates the last time the user logged in
		 */
		if ($this->user_id) {
			$sql = "UPDATE users SET last_logged_in = NOW() WHERE user_id = '$this->user_id' LIMIT 1";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			return true;
		}
		else {
			return false;
		}
	}

	final public function change_reputation(ReputationChange $change) {
		/*
		(ReputationChange) -> Bool
		Applies the reputation change to the current user
		 */
		throw new PemissionException('You do not have permission as an anonymous user to attempt this action');
	}

	final private function log_reputation_change(ReputationChange $change) {
		/*
		(ReputationChange) -> Bool
		Logs the reputation change into the database
		 */
		throw new PemissionException('You do not have permission as an anonymous user to attempt this action');
	}

	public function user_joined(BadmintonDate $badminton_date) {
		/*
		(User) -> Bool
		Checks if the current user has joined the badminton date
		 */
		throw new PemissionException('You do not have permission as an anonymous user to attempt this action');
	}

	final public function join_badminton_date(Date $badminton_date) {
		/*
		(Date) -> Bool
		The current user attempts to join the badminton datr
		 */
		throw new PemissionException('You do not have permission as an anonymous user to attempt this action');
	}

	final public function cancel_date(BadmintonDate $badminton_date) {
		/*
		(Date) -> Bool
		If the current user is the creator, will attempt to cancel the badminton date, usually because unable to book or cannot go
		 */
		throw new PemissionException('You do not have permission as an anonymous user to attempt this action');
	}

	final public function log_action(Action $action) {
		/*
		(Action) -> Bool
		With a given action, logs it into the user actions database
		 */
		throw new BadMethodCallException; //Not yet implemented
	}

	final public function report_fake_date(ConfirmedDate $badminton_date) {
		/*
		(ConfirmedDate) -> Bool
		Report the date as faked, no courts were actually booked or know for sure it is fake.
		 */
		throw new PemissionException('You do not have permission as an anonymous user to attempt this action');
	}

	final public function notify_absence(BadmintonDate $badminton_date) {
		/*
		Think about this function
		 */
		throw new PemissionException('You do not have permission as an anonymous user to attempt this action');
	}

	public function create_group(Group $group_to_be_created) {
		/*
		(Group) -> Bool
		Attempts to create a group with the given parameters
		 */
		throw new PemissionException('You do not have permission as an anonymous user to attempt this action');
	}

	public function mark_user_as_absent(User $user, ConfirmedDate $badminton_date) {
		/*
		(User) -> Bool
		Marks a user as absent from an event
		 */
		throw new PemissionException('You do not have permission as an anonymous user to attempt this action');
	}

	public function mark_user_as_present(User $user, ConfirmedDate $badminton_date) {
		/*
		(User, ConfirmedDate) -> Bool
		Marks a user as present for a confirmed badminton date that has already passed
		 */
		throw new PemissionException('You do not have permission as an anonymous user to attempt this action');
	}

	public function endorse_user(User $user_to_endorse) {
		/*
		(User) -> Bool
		The purpose of this function is to give the user some reputation points in order to show that this user comes and shows regularly or maybe plays well
		 */
		throw new PemissionException('You do not have permission as an anonymous user to attempt this action');
	}

	final public function get_activity() {
		/*
		(Null) -> Null
		Attempts to get the user activity
		 */
		throw new BadMethodCallException('You do not have permission as an anonymous user to attempt this action');
	}

}