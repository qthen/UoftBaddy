<?php
abstract class PendingAction {
	/*
	Some class for some pending action on the site
	*/

	const ACTIVE = 1;
	const INACTIVE = 0;

	public function destory_self() {
		/*
		(Null) -> Bool
		Inactivates the PendingEndorsementToken
		*/
		if ($this->token_id) {
			$mysqli = Database::connection();
			$sql = "UPDATE `pending_actions` SET active = '" . self::INACTIVE . "' WHERE token_id = '" . $this->token_id . "'";
			$result = $mysqli->query($sql)
			or die ($mysqli->error);
			return true;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call ' . __METHOD__ . ' because the token id is invalid');
		}
	}

	public function resurrect() {
		/*
		(Null) -> Bool
		Activates the token
		*/
	}

	public function is_valid() {
		/*
		(Null) -> Bool
		Returns whether the current pending action token is valid
		*/
		if ($this->token_id) {
			$sql = "SELECT token_id FROM `pending_actions` WHERE token_id = '$this->token_id' AND active = '" . self::ACTIVE . "'";
			$result = Database::connection()->query($sql)
			or die (Database::connection()->error);
			return $result->num_rows == 1; 
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call ' . __METHOD__ . ' because the token id is invalid');
		}
	}
}

class PendingEndorsementToken {
	const ABSENT = -20;
	const REP_UP = 10;

	public static $defaults = array(
		'token_id' => null,
		'user_to_endorse' => null,
		'date_issued' => null,
		'awaiting_user' => null,
		'cause_date' => null,
		'reputation_change' => 0
	);

	public function __construct(array $args = array()) {
		$defaults = self::$defaults;
		$args = array_merge($defaults, $args);

		$this->token_id = (is_numeric($args['token_id'])) ? $args['token_id'] : null;
		$this->user_to_endorse = (is_a($args['user_to_endorse'], 'User')) ? $args['user_to_endorse'] : null;
		$this->awaiting_user = (is_a($args['awaiting_user'], 'User')) ? $args['awaiting_user'] : null;
		$this->date_issued = $args['date_issued'];
		$this->cause_date = (is_a($args['cause_date'], 'BadmintonDate')) ? $args['cause_date'] : null;
		$this->reputation_change = (is_numeric($args['reputation_change'])) ? $args['reputation_change'] : 0;

	}
}