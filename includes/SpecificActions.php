<?php
//All classes for specific actions on the site which extends some abstract class in the Actions.php file
//require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/Actions.php';

class JoinSite extends SiteAction {
	/*
	Class for joining the site
	*/
	private $dbc;
	public $joiner, $conversation, $action_id;

	public static $defaults = array(
		'joiner' => null,
		'action_id' => null,
		'date_action' => null,
		'trigger' => null
	);

	public function __construct(array $args = array()) {
		$defaults = self::$defaults;
		$args = array_merge($defaults, $args);

		$this->dbc = Database::connection();
		$this->action_id = (is_numeric($args['action_id'])) ? $args['action_id'] : null;
		$this->joiner(is_a($args['joiner'], 'User')) ? $args['joiner'] : null;
		$this->date_action = $args['date_action'];
		$this->trigger = (is_a($args['trigger'], 'User')) ? $args['trigger'] : null;
	}

	public function log_action() {
		/*
		(Null) -> Int
		Logs self into the database and returns the inserted id
		 */
		if ($this->joiner->user_id && $conversation->conversation_id) {
			$sql = "INSERT INTO `actions` (user_id, date_action, type) VALUES ('$this->joiner->user_id', NOW(), '" . ActionFactory::$action_type_contract['JoinSite'] . "')";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			//echo 'Inserted id is ' . $this->dbc->insert_id;
			return $this->dbc->insert_id;
		}
		else {
			return false;
		}
	}

}


class JoinDiscussion extends MessageAction {
	/*
	Class for joining a event's discussion
	 */
	private $dbc;
	public $joiner, $conversation, $action_id;

	public static $defaults = array(
		'joiner' => null,
		'conversation' => null,
		'action_id' => null,
		'date_action' => null
	);

	public function __construct(array $args = array()) {
		$defaults = self::$defaults;
		$args = array_merge($defaults, $args);

		$this->dbc = Database::connection();
		$this->action_id = (is_numeric($args['action_id'])) ? $args['action_id'] : null;
		$this->joiner(is_a($args['joiner'], 'User')) ? $args['joiner'] : null;
		$this->conversation = (is_a($args['conversation'], 'Conversation')) ? $args['conversation'] : null;
		$this->date_action = $args['date_action'];
	}

	public function log_action() {
		/*
		(Null) -> Int
		Logs self into the database and returns the inserted id
		 */
		if ($this->joiner->user_id && $conversation->conversation_id) {
			$sql = "INSERT INTO `actions` (user_id, date_action, type) VALUES ('$this->joiner->user_id', NOW(), '" . ActionFactory::$action_type_contract['JoinDiscussion'] . "')";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			echo 'Inserted id is ' . $this->dbc->insert_id;
			return $this->dbc->insert_id;
		}
		else {
			return false;
		}
	}
}


class ApproveJoinRequest extends MessageAction {
	/*
	Class for approving a user's request to join a conversation
	 */
	private $dbc;
	public $joiner, $approver;

	public function __construct(array $args = array()) {
		$defaults = self::$defaults;
		$args = array_merge($defaults, $args);

		$this->dbc = Database::connection();
		$this->joiner = (is_a($args['joiner'], 'User')) ? $args['joiner'] : null;
		$this->conversation = (is_a($args['conversation'], 'Conversation')) ? $args['conversation'] : null;
		$this->approver = (is_a($args['approver'], 'User')) ? $args['approver'] : null;
		$this->date_action = $args['date_action'];
	}

	public function log_action() {
		return true;
	}
}



class JoinThread extends ThreadAction {
	/*
	Class for joining a thread
	*/
	private $dbc;
	public $thread, $poster;

	public static $defaults = array(
		'joiner' => null,
		'thread' => null,
		'action_id' => null,
		'date_action' => null,
		'trigger' => null
	);

	public function __construct(array $args = array()) {
		$defaults = self::$defaults;
		$args = array_merge($defaults, $args);
		$this->dbc = Database::connection();
		$this->action_id = (is_numeric($args['action_id'])) ? $args['action_id'] : null;
		$this->joiner = (is_a($args['joiner'], 'User')) ? $args['joiner'] : null;
		$this->thread = (is_a($args['thread'], 'Thread')) ? $args['thread'] : null;
		$this->date_action = $args['date_action'];
		$this->trigger = (is_a($args['trigger'], 'User')) ? $args['trigger'] : null;
	}

	public function log_action() {
		/*
		(Null) -> Int 
		Attempts to log self into the database and returns the insert id
		 */
		if ($this->poster->user_id && $this->thread->thread_id) {
			$sql = "INSERT INTO `actions` (user_id, date_action, type) VALUES ('{$this->poster->user_id}', NOW(), '" . ActionFactory::$action_type_contract['PostedThread'] . "')";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			return $this->dbc->insert_id;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on request log_action');
		}
	}	
}


class PostedThread extends ThreadAction {
	/*
	Class for posting a thread
	 */
	private $dbc;
	public $thread, $poster;

	public static $defaults = array(
		'poster' => null,
		'thread' => null,
		'action_id' => null,
		'date_action' => null,
		'trigger' => null
	);

	public function __construct(array $args = array()) {
		$defaults = self::$defaults;
		$args = array_merge($defaults, $args);
		$this->dbc = Database::connection();
		$this->action_id = (is_numeric($args['action_id'])) ? $args['action_id'] : null;
		$this->poster = (is_a($args['poster'], 'User')) ? $args['poster'] : null;
		$this->thread = (is_a($args['thread'], 'Thread')) ? $args['thread'] : null;
		$this->date_action = $args['date_action'];
		$this->trigger = (is_a($args['trigger'], 'User')) ? $args['trigger'] : null;
	}

	public function log_action() {
		/*
		(Null) -> Int 
		Attempts to log self into the database and returns the insert id
		 */
		if ($this->poster->user_id && $this->thread->thread_id) {
			$sql = "INSERT INTO `actions` (user_id, date_action, type) VALUES ('{$this->poster->user_id}', NOW(), '" . ActionFactory::$action_type_contract['PostedThread'] . "')";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			return $this->dbc->insert_id;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on request log_action');
		}
	}	
}


class PostedCommentOnThread extends ThreadAction {
	/*
	A comment posted on a thread for some participant
	 */
	private $dbc;
	public $thread, $commenter;

	public static $defaults = array(
		'commeter' => null,
		'thread' => null,
		'action_id' => null,
		'trigger' => null,
		'date_action' => null
	);

	public function __construct(array $args = array()) {
		$defaults = self::$defaults;
		$args = array_merge($defaults, $args);
		$this->dbc = Database::connection();
		$this->action_id = (is_numeric($args['action_id'])) ? $args['action_id'] : null;
		$this->commenter = (is_a($args['commenter'], 'User')) ? $args['commenter'] : null;
		$this->thread = (is_a($args['thread'], 'Thread')) ? $args['thread'] : null;
		$this->date_action = $args['date_action'];
		$this->trigger = (is_a($args['trigger'], 'User')) ? $args['trigger'] : null;
	}

	public function log_action() {
		/*
		(Null) -> Int 
		Attempts to log self into the database and returns the insert id
		 */
		if ($this->commenter->user_id && $this->thread->thread_id) {
			$sql = "INSERT INTO `actions` (user_id, date_action, type) VALUES ('{$this->commenter->user_id}', NOW(), '" . ActionFactory::$action_type_contract['PostedCommentOnThread'] . "')";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			return $this->dbc->insert_id;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on request log_action');
		}
	}
}





class ProposeBadmintonDate extends BadmintonDateAction {
	/*
	Class for proposing some badminton date
	 */
	private $dbc;
	public $joiner, $badminton_date;

	public static $defaults = array(
		'proposer' => null,
		'badminton_date' => null,
		'action_id' => null,
		'date_action' => null,
		'trigger' => null
	);

	public function __construct(array $args = array()) {
		$defaults = self::$defaults;
		$args = array_merge($defaults, $args);
		$this->dbc = Database::connection();
		$this->action_id = (is_numeric($args['action_id'])) ? $args['action_id'] : null;
		$this->proposer = (is_a($args['proposer'], 'User')) ? $args['proposer'] : null;
		$this->badminton_date = (is_a($args['badminton_date'], 'BadmintonDate')) ? $args['badminton_date'] : null;
		$this->date_action = $args['date_action'];
		$this->trigger = (is_a($args['trigger'], 'User')) ? $args['trigger'] : null;
	}

	public function log_action() {
		/*
		(Null) -> Int 
		Attempts to log self into the database and returns the insert id
		 */
		if ($this->proposer->user_id && $this->badminton_date->date_id) {
			$user_id = $this->proposer->user_id;
			$sql = "INSERT INTO `actions` (user_id, date_action, type) VALUES ('$user_id', NOW(), '" . ActionFactory::$action_type_contract['ProposeBadmintonDate'] . "')";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			return $this->dbc->insert_id;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on request log_action');
		}
	}
}


class LeaveBadmintonDate extends BadmintonDateAction {
	/*
	Class for an action of leaving a badminton date
	 */
	private $dbc;
	public $joiner, $badminton_date;

	public static $defaults = array(
		'leaver' => null,
		'badminton_date' => null,
		'action_id' => null,
		'trigger' => null,
		'date_action' => null
	);

	public function __construct(array $args = array()) {
		$defaults = self::$defaults;
		$args = array_merge($defaults, $args);
		$this->dbc = Database::connection();
		$this->action_id = (is_numeric($args['action_id'])) ? $args['action_id'] : null;
		$this->leaver = (is_a($args['leaver'], 'User')) ? $args['leaver'] : null;
		$this->badminton_date = (is_a($args['badminton_date'], 'BadmintonDate')) ? $args['badminton_date'] : null;
		$this->date_action = $args['date_action'];
		$this->trigger = (is_a($args['trigger'], 'User')) ? $args['trigger'] : null;
	}

	public function log_action() {
		/*
		(Null) -> Int 
		Attempts to log self into the database and returns the insert id
		 */
		if ($this->joiner->user_id && $this->badminton_date->date_id) {
			$sql = "INSERT INTO `actions` (user_id, date_action, type) VALUES ('$this->leaver->user_id', NOW(), '" . ActionFactory::$action_type_contract['LeaveBadmintonDate'] . "')";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			return true;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on request log_action');
		}
	}	
}


class JoinBadmintonDate extends BadmintonDateAction {
	/*
	Class for joining a badminton date
	 */
	private $dbc;
	public $joiner, $badminton_date, $trigger;

	public static $defaults = array(
		'joiner' => null,
		'badminton_date' => null,
		'action_id' => null,
		'trigger' => null,
		'date_action' => null
	);

	public function __construct(array $args = array()) {
		$defaults = self::$defaults;
		$args = array_merge($defaults, $args);
		$this->dbc = Database::connection();
		$this->action_id = (is_numeric($args['action_id'])) ? $args['action_id'] : null;
		$this->joiner = (is_a($args['joiner'], 'User')) ? $args['joiner'] : null;
		$this->badminton_date = (is_a($args['badminton_date'], 'BadmintonDate')) ? $args['badminton_date'] : null;
		$this->trigger = (is_a($args['trigger'], 'User')) ? $args['trigger'] : null;
		$this->date_action = $args['date_action'];
	}

	public function log_action() {
		/*
		(Null) -> Int 
		Attempts to log self into the database and returns the insert id
		 */
		if ($this->joiner->user_id && $this->badminton_date->date_id) {
			$sql = "INSERT INTO `actions` (user_id, date_action, type) VALUES ('" . $this->joiner->user_id . "', NOW(), '" . ActionFactory::$action_type_contract['JoinBadmintonDate'] . "')";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			return $this->dbc->insert_id;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on request log_action');
		}
	}
}


class ConfirmDate extends BadmintonDateAction {
	/*
	Action for confirming a previously tentative date
	 */
	public function log_action() {
		return true;
	}
}


class CreateDate extends BadmintonDateAction {
	/*
	Action for creating a date of any kind
	 */
	
	public function log_action() {
		return true;
	}
	
}
?>