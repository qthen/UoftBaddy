<?php
//Pushing factories and service classes for pushing actions into the database
abstract class ActionPusher {
	/*
	Class for pushing actions in the database, easily
	 */
	public static function push_action(Action $action) {
		/*
		(Action) -> Bool
		Attempts to push an action into the database
		 */
		$class = get_class($action);
		switch ($class) {
			case 'JoinDiscussion':
			case 'ApproveJoinRequest':
				ConversationActionPusher::push_notification($action);
				break;
			case 'JoinBadmintonDate':
			case 'LeaveBadmintonDate':
			case 'ProposeBadmintonDate':
				BadmintonDateActionPusher::push_action($action);
				break;
			case 'PostedCommentOnThread':
			case 'PostedThread':
				ThreadActionPusher::push_action($action);
				break;
			default:
				throw new UnexpectedValueException("$class is not a valid action class, tried to push in ActionPusher");
		}
	}
}

abstract class ThreadActionPusher extends ActionPusher {
	/*
	Handles pushing actions from threads
	 */
	public static function push_action(Action $action) {
		$mysqli = Database::connection();
		$class = get_class($action);
		switch ($class) {
			case 'PostedCommentOnThread':
				$action_id = $action->log_action();
				$keys = ActionFactory::$action_key_values[$class];
				foreach ($keys as $key) {
					$value = ActionFactory::handle_key($action, $key);
					$insert = "INSERT INTO `action_key_values` (action_id, `key`, value) VALUES ('$action_id', '$key', '$value')";
					$result = $mysqli->query($insert)
					or die ($mysqli->error);
				}
				break;
			case 'PostedThread':
				$action_id = $action->log_action();
				$keys = ActionFactory::$action_key_values[$class];
				foreach ($keys as $key) {
					$value = ActionFactory::handle_key($action, $key);
					$insert = "INSERT INTO `action_key_values` (action_id, `key`, value) VALUES ('$action_id', '$key', '$value')";
					$result = $mysqli->query($insert)
					or die ($mysqli->error);
				}
				break;
			default:
				throw new OutOfRangeException('OutOfRangeException occured on pushing action in ThreadActionPusher with classs ' . $class);
		}
	}
}


abstract class BadmintonDateActionPusher extends ActionPusher {
	/*
	Handles pushing actions from badminton dates
	 */
	public static function push_action(Action $action) {
		/*
		(Action) -> Bool
		Pushes the BadmintonDateAction into the database
		 */
		$mysqli = Database::connection();
		$class = get_class($action);
		switch ($class) {
			case 'JoinBadmintonDate':
				$action_id = $action->log_action();
			//	echo 'Action id is ' . $action_id;
				$keys = ActionFactory::$action_key_values[$class];
				foreach ($keys as $key) {
					$value = ActionFactory::handle_key($action, $key);
					$insert = "INSERT INTO `action_key_values` (action_id, `key`, value) VALUES ('$action_id', '$key', '$value')";
					$result = $mysqli->query($insert)
					or die ($mysqli->error);
				}
				break;
			case 'ProposeBadmintonDate': 
				$action_id = $action->log_action();
			//	echo 'Action id is ' . $action_id;
				$keys = ActionFactory::$action_key_values[$class];
				foreach ($keys as $key) {
					$value = ActionFactory::handle_key($action, $key);
					$insert = "INSERT INTO `action_key_values` (action_id, `key`, value) VALUES ('$action_id', '$key', '$value')";
					$result = $mysqli->query($insert)
					or die ($mysqli->error);
				}
				break;
			case 'LeaveBadmintonDate':
				$action_id = $action->log_action();
				$keys = ActionFactory::$action_key_values[$class];
				foreach ($keys as $key) {
					$value = ActionFactory::handle_key($action, $key);
					$insert = "INSERT INTO `action_key_values` (action_id, `key`, value) VALUES ('$action_id', '$key', '$value')";
					$result = $mysqli->query($insert)
					or die ($mysqli->error);
				}
				break;
			default:
				throw new UnexpectedValueException("$class is not a valid action class, tried to push in BadmintonDateActionPusher");
		}
	}
}


abstract class ConversationActionPusher extends ActionPusher {
	/*
	Handles pushing actions from conversations into the database
	 */
	const JOIN_MESSAGE = '';

	public static function push_action(Action $action) {
		$mysqli = Database::connection();
		$class = get_class($action);
		switch ($class) {
			case 'JoinDiscussion':
				//Insert into the database the correct key value pairs
				$action_id = $action->log_action();
				$keys = ActionFactory::$action_key_values($class);
				foreach ($keys as $key) {
					$value = ActionFactory::handle_key($action, $keys);
					$insert = "INSERT INTO `action_key_values` (action_id, key, value) VALUES ('$action_id', '$key', '$value')";
					$result = $mysqli->query($insert)
					or die ($mysqli->error);
				}
				return true;
				break;
			case 'ApproveJoinRequest':
				//No need to push action for this one
				return true;
				break;
		}

	}
}