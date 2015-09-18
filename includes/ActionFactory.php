<?php
//ActionFactory responsible for all the helper methods and services for creating, generating, or pushing actions. Base class which should be included
class ActionFactory {
	/*
	Class for generating all database changes due to actions preformed and notifications
	*/

	public static $action_key_values = array(
		'JoinDiscussion' => array(
			'conversation_id', 
		),
		'JoinBadmintonDate' => array(
			'date_id'
		),
		'ProposeBadmintonDate' => array(
			'date_id'
		),
		'LeaveBadmintonDate' => array(
			'date_id'
		),
		'PostedCommentOnThread' => array(
			'thread_id'
		),
		'PostedThread' => array(
			'thread_id'
		),
		'JoinSte' => array(
			'joiner_id'
		)
	);

	public static $action_type_contract = array(
		'JoinDiscussion' => 1,
		'ApproveJoinRequest' => 2,
		'JoinBadmintonDate' => 3,
		'ProposeBadmintonDate' => 4,
		'LeaveBadmintonDate' => 5,
		'PostedCommentOnThread' => 6,
		'PostedThread' => 7,
		'JoinSite' => 8
	);

	public static function fetch_sitewide_activity() {
		/*
		(Null) -> Array of Actions
		Fetches the site wide activity
		*/
		$mysqli = Database::connection();
		$sql = "SELECT t2.action_id, t1.key, t1.value, t2.type, t2.user_id, t2.date_action
		FROM `action_key_values` as t1 
		INNER JOIN `actions` as t2 
		ON t2.action_id = t1.action_id
		ORDER BY t2.date_action DESC";
		$result = $mysqli->query($sql)
		or die ($mysqli->error);
		$current_action_id = null;
		$errored_action_id = null;
		$current_action_type = null;
		$current_action_array_constructor = array();
		$actions = array(); //Array for all the actions user did
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			if (is_null($current_action_id) && is_null($current_action_type)) {
				//First iteration of the loop, set it
				$current_action_id = $row['action_id'];
				$current_action_type = $row['type'];
				$current_action_date = $row['date_action'];
			}
			//echo 'Action id is ' . $row['action_id'];
			//echo $errored_action_id;
			if ($errored_action_id != $row['action_id']) {
				//echo 'hi';
				if ($current_action_id != $row['action_id']) {
					//Create the previous action before beginning
					$class_name = array_search($current_action_type, self::$action_type_contract);
					if ($class_name) {
						//Here we will push the action_id as well as the date
						$current_action_array_constructor['date_action'] = $current_action_date;
						$current_action_array_constructor['action_id'] = $current_action_id;
						$action = new $class_name($current_action_array_constructor);

						//var_dump($action);

						//Get the fields from the trigger
						$action->trigger->get_fields();

						//Generate a quick message
						switch ($class_name) {
							case 'PostedThread':
								$action->message = sprintf(ActionHelper::$action_messages[$class_name], $action->trigger->user_id, $action->trigger->username, $action->thread->thread_id);
								break;
							case 'PostedCommentOnThread':
								$action->message = sprintf(ActionHelper::$action_messages[$class_name], $action->trigger->user_id, $action->trigger->username, $action->trigger->thread_id);
								break;
							case 'JoinSite':
								//Get the difference ago
								$ago = time() - strtotime($current_action_array_constructor['date_action']);
								$ago = gmdate("H:i:s", $ago);
								$action->message = sprintf(ActionFactory::$action_messages[$class_name], $action->trigger->user_id, $action->trigger->username, $ago);
								break;
							case 'ProposeBadmintonDate':
								$action->message = sprintf(ActionHelper::$action_messages[$class_name], $action->trigger->user_id, $action->trigger->username, $action->badminton_date->date_id, $action->badminton_date->datename);
								break;
							case 'JoinBadmintonDate':
								$action->message = sprintf(ActionHelper::$action_messages[$class_name], $action->trigger->user_id, $action->trigger->username, $action->badminton_date->date_id, $action->badminton_date->datename);
								break;
						}
						array_push($actions, $action);
					}
					//Set the new values
					$current_action_id = $row['action_id'];
					$current_action_type = $row['type'];
					$current_action_date = $row['date_action'];
				}
				$class_name = array_search($row['type'], self::$action_type_contract);
				if ($class_name == false) {
					//echo 'false';
					//Error creating the class, skip the iteration for all instances of this action id
					$errored_action_id = $row['action_id'];
				}
				else {
					//Here is ONLY for parsing the key=>value pairs in the DB, not for t2 information
					//No error, continue grabbing
					//echo $row['key'];
					if (!isset($current_action_array_constructor['trigger'])) {
						//The trigger user_id is the user_id that cause the action
						$trigger_user = new ProfileUser(array(
							'user_id' => $row['user_id'])
						);
						$current_action_array_constructor['trigger'] = $trigger_user;
					}
					switch ($row['key']) {
						case 'joiner_id':
							$sql = "SELECT user_id, username, email, reputation, avatar FROM `users` WHERE user_id = '" . $row['value'] . "'";
							$result = $mysqli->query($sql)
							or die ($mysqli->error);
							$row_joiner = $result->fetch_array(MYSQLI_ASSOC);
							$joiner = new ProfileUser($row_joiner);
							$current_action_array_constructor['joiner'] = $joiner;
							break;
						case 'date_id':
							$sql = "SELECT t1.date_id, t1.datename, t1.begin_datetime, t1.end_datetime, t1.bool_group, t1.confirmed, t1.creator_id as `user_id`, t2.username, t2.email, t2.reputation, t2.avatar
							FROM `badminton_dates` as t1 
							LEFT JOIN `users` as t2 
							ON t2.user_id = t1.creator_id
							WHERE t1.date_id = '" . $row['value']. "'";
							$result_date = $mysqli->query($sql)
							or die ($mysqli->error);
							$row_date = mysqli_fetch_array($result_date);
							$row_date['creator'] = new ProfileUser($row_date);
							if ($row_date['confirmed']) {
								$badminton_date = new ConfirmedBadmintonDate($row_date);
							}
							else {
								$badminton_date = new PublicProposedDate($row_date);
							}
							$current_action_array_constructor['badminton_date'] = $badminton_date;

							//Also create the joiner/proposers
							if ($class_name == 'ProposeBadmintonDate') {
								$current_action_array_constructor['proposer'] = $user;
							}
							else if ($class_name == 'JoinBadmintonDate') {
								$current_action_array_constructor['joiner'] = $user;
							}
							else if ($class_name == 'LeaveBadmintonDate') {
								$current_action_array_constructor['leaver'] = $user;
							}
							break;
						case 'conversation_id':
							//echo 'chosen';
							$sql = "SELECT conversation_id, conversation_name, date_started, date_recent_activity FROM `conversations` WHERE conversation_id = '". $row['value'] . "'";
							$result_convo = $mysqli->query($sql)
							or die ($mysqli->error);
							$row_convo = mysqli_fetch_array($result_convo, MYSQLI_ASSOC);
							$current_action_array_constructor['conversation'] = new Conversation($row_convo);
							break;
						case 'thread_id':
							$sql = "SELECT t1.thread_id, t1.thread_title, t1.thread_text
							FROM threads as t1 
							WHERE t1.thread_id = '" . $row['value'] . "'";
							$result_thread = $mysqli->query($sql)
							or die ($mysqli->error);
							if ($result_thread->num_rows == 1) {
								$row_thread = mysqli_fetch_array($result_thread, MYSQLI_ASSOC);
								$current_action_array_constructor['thread'] = new Thread($row_thread);
							}
							else {
								$errored_action_id = $row['action_id'];
								$current_action_array_constructor = array();
							}
							break;
						default:
							$errored_action_id = $row['action_id'];
							//echo $errored_action_id;
							$current_action_array_constructor = array();
							break;
					}
				}
			}
			else {
				continue;
			}
		}
		//var_dump($actions);
		return $actions;
	}

	public static function fetch_activity($user) {
		/*
		(User) -> Array
		Fetches the user activity in an array of html strings
		 */
		$user->get_fields();
		$class = get_class($user);
		if (($class == 'ProfileUser') || ($class == 'CurrentUser')) {
			$mysqli = Database::connection();
			$sql = "SELECT t2.action_id, t1.key, t1.value, t2.type, t2.user_id, t2.date_action
			FROM `action_key_values` as t1 
			INNER JOIN `actions` as t2 
			ON t2.action_id = t1.action_id
			AND t2.user_id = '$user->user_id'
			ORDER BY t2.date_action DESC";
			//echo $sql;
			$result = $mysqli->query($sql)
			or die ($mysqli->error);
			$current_action_id = null;
			$errored_action_id = null;
			$current_action_type = null;
			$current_action_array_constructor = array();
			$actions = array(); //Array for all the actions user did
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				if (is_null($current_action_id) && is_null($current_action_type)) {
					//First iteration of the loop, set it
					$current_action_id = $row['action_id'];
					$current_action_type = $row['type'];
					$current_action_date = $row['date_action'];
				}
				//echo 'Action id is ' . $row['action_id'];
				//echo $errored_action_id;
				if ($errored_action_id != $row['action_id']) {
					//echo 'hi';
					if ($current_action_id != $row['action_id']) {
						//Create the previous action before beginning
						$class_name = array_search($current_action_type, self::$action_type_contract);
						if ($class_name) {
							//Here we will push the action_id as well as the date
							$current_action_array_constructor['date_action'] = $current_action_date;
							$current_action_array_constructor['action_id'] = $current_action_id;
							$action = new $class_name($current_action_array_constructor);

							//var_dump($action);

							//Get the fields from the trigger
							$action->trigger->get_fields();

							//Generate a quick message
							switch ($class_name) {
								case 'PostedThread':
									$action->message = sprintf(ActionHelper::$action_messages[$class_name], $action->trigger->user_id, $action->trigger->username, $action->thread->thread_id, $action->thread->thread_title);
									break;
								case 'PostedCommentOnThread':
									$action->message = sprintf(ActionHelper::$action_messages[$class_name], $action->trigger->user_id, $action->trigger->username, $action->trigger->thread_id, $action->thread->thread_title);
									break;
								case 'JoinSite':
									//Get the difference ago
									$ago = time() - strtotime($current_action_array_constructor['date_action']);
									$ago = gmdate("H:i:s", $ago);
									$action->message = sprintf(ActionFactory::$action_messages[$class_name], $action->trigger->user_id, $action->trigger->username, $ago);
									break;
								case 'ProposeBadmintonDate':
									$action->message = sprintf(ActionHelper::$action_messages[$class_name], $action->trigger->user_id, $action->trigger->username, $action->badminton_date->date_id, $action->badminton_date->datename);
									break;
								case 'JoinBadmintonDate':
									$action->message = sprintf(ActionHelper::$action_messages[$class_name], $action->trigger->user_id, $action->trigger->username, $action->badminton_date->date_id, $action->badminton_date->datename);
									break;
							}
							array_push($actions, $action);
						}
						//Set the new values
						$current_action_id = $row['action_id'];
						$current_action_type = $row['type'];
						$current_action_date = $row['date_action'];
					}
					$class_name = array_search($row['type'], self::$action_type_contract);
					if ($class_name == false) {
						//echo 'false';
						//Error creating the class, skip the iteration for all instances of this action id
						$errored_action_id = $row['action_id'];
					}
					else {
						//Here is ONLY for parsing the key=>value pairs in the DB, not for t2 information
						//No error, continue grabbing
						//echo $row['key'];
						if (!isset($current_action_array_constructor['trigger'])) {
							//The trigger user_id is the user_id that cause the action
							$trigger_user = new ProfileUser(array(
								'user_id' => $row['user_id'])
							);
							$current_action_array_constructor['trigger'] = $trigger_user;
						}
						switch ($row['key']) {
							case 'joiner_id':
								$sql = "SELECT user_id, username, email, reputation, avatar FROM `users` WHERE user_id = '" . $row['value'] . "'";
								$result = $mysqli->query($sql)
								or die ($mysqli->error);
								$row_joiner = $result->fetch_array(MYSQLI_ASSOC);
								$joiner = new ProfileUser($row_joiner);
								$current_action_array_constructor['joiner'] = $joiner;
								break;
							case 'date_id':
								$sql = "SELECT t1.date_id, t1.datename, t1.begin_datetime, t1.end_datetime, t1.bool_group, t1.confirmed, t1.creator_id as `user_id`, t2.username, t2.email, t2.reputation, t2.avatar
								FROM `badminton_dates` as t1 
								LEFT JOIN `users` as t2 
								ON t2.user_id = t1.creator_id
								WHERE t1.date_id = '" . $row['value']. "'";
								$result_date = $mysqli->query($sql)
								or die ($mysqli->error);
								$row_date = mysqli_fetch_array($result_date);
								$row_date['creator'] = new ProfileUser($row_date);
								if ($row_date['confirmed']) {
									$badminton_date = new ConfirmedBadmintonDate($row_date);
								}
								else {
									$badminton_date = new PublicProposedDate($row_date);
								}
								$current_action_array_constructor['badminton_date'] = $badminton_date;

								//Also create the joiner/proposers
								if ($class_name == 'ProposeBadmintonDate') {
									$current_action_array_constructor['proposer'] = $user;
								}
								else if ($class_name == 'JoinBadmintonDate') {
									$current_action_array_constructor['joiner'] = $user;
								}
								else if ($class_name == 'LeaveBadmintonDate') {
									$current_action_array_constructor['leaver'] = $user;
								}
								break;
							case 'conversation_id':
								echo 'chosen';
								$sql = "SELECT conversation_id, conversation_name, date_started, date_recent_activity FROM `conversations` WHERE conversation_id = '". $row['value'] . "'";
								$result_convo = $mysqli->query($sql)
								or die ($mysqli->error);
								$row_convo = mysqli_fetch_array($result_convo, MYSQLI_ASSOC);
								$current_action_array_constructor['conversation'] = new Conversation($row_convo);
								break;
							case 'thread_id':
								$sql = "SELECT t1.thread_id, t1.thread_title, t1.thread_text
								FROM threads as t1 
								WHERE t1.thread_id = '" . $row['value'] . "'";
								$result_thread = $mysqli->query($sql)
								or die ($mysqli->error);
								$row_thread = mysqli_fetch_array($result_thread, MYSQLI_ASSOC);
								$current_action_array_constructor['thread'] = new Thread($row_thread);
								break;
							default:
								$errored_action_id = $row['action_id'];
								//echo $errored_action_id;
								$current_action_array_constructor = array();
								break;
						}
					}
				}
				else {
					continue;
				}
			}
			//var_dump($actions);
			return $actions;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call fetch_acitivy');
		}
	}


	public static function handle_key(Action $object, $key) {
		/*
		(Action, String) -> Bool
		Given the key, handles the data feed and inserts it into the database based on the object
		 */
		if ($key == 'conversation_id') {
			return $object->conversation->conversation_id;
		}
		elseif ($key == 'joiner_id') {
			return $object->joiner->user_id;
		}
		elseif ($key == 'date_id') {
			return $object->badminton_date->date_id;
		}
		elseif ($key == 'thread_id') {
			return $object->thread->thread_id;
		}
	}
}

class ActionHelper extends ActionFactory {
	/*
	Class for generating the messages of actions
	 */
	
	public static $action_messages = array(
		'PostedThread' => '<a href="profile.php?id=%s">%s</a> posted a <a href="thread.php?id=%s">thread</a>',
		'PostedCommentOnThread' => '<a href="profile.php?id=%s">%s</a> commented on your <a href="thread.php?id=%s">thread</a>',
		'JoinSite' => '<a href="profile.php?id=%s">%s</a> joined the site %s ago',
		'ProposeBadmintonDate' => '<a href="profile.php?id=%s">%s</a> created badminton event - <a href="date.php?id=%s">%s</a>',
		'JoinBadmintonDate' => '<a href="profile.php?id=%s">%s</a> joined badminton event - <a href="date.php?id=%s">%s</a>'
	);
}

?>