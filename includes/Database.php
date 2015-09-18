<?php
require_once __DIR__ . '/../vars/constants.php';


class Database {
	/*
	Class for the database
	 */

	private static $connection;

	public static function connection() {
		/*
		Provides a connection variable for usage
		 */
		require_once __DIR__ . '/../vars/connectvars.php';
		if (!self::$connection) {
			$mysqli = new mysqli(HOST, USER, PASS, DATABASE);
			self::$connection = $mysqli;
			return self::$connection;
		}
		else {
			return self::$connection;
		}
	}

	public static function secondsToTime($seconds) {
	    $dtF = new DateTime("@0");
	    $dtT = new DateTime("@$seconds");
	    return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
	}

	public static function print_exception(Exception $e) {
		echo json_encode(array(
			'message' => $e->getMessage(),
			'line' => $e->getLine(),
			'file' => $e->getFile()),
		JSON_PRETTY_PRINT
		);
	}

	public static function sanitize($input) {
		/*
		(Mixed) -> Mixed
		 */
		$mysqli = self::connection();
		if (is_array($input)) {
			foreach ($input as $key=>$val) {
				$input[$key] = mysqli_real_escape_string($mysqli, trim($val));
			}
		}
		else {
			$input = mysqli_real_escape_string($mysqli, trim($input));
		}
		return $input;
	}

	public function manipulate_reputation(User $user, ReputationChange $change) {
		/*
		(User, ReputationChange) -> Bool
		Attempts to apply the reputation change to a user and records it
		 */
		if ($user->user_id && $change->reputation_change) {
			
		}
	}
}