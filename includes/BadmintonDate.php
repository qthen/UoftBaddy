<?php
require_once $_SERVER['DOCUMENT_ROOT'] .'/vars/constants.php'; 


abstract class BadmintonDate {
	/*
	Abstract class for some date that is desired to have badminton on
	 */
	
	private $dbc;
	public $date_id, $datename, $datetime, $creator, $attendees, $cancelled, $confirmed, $db_type, $location;

	const LEAVE_DEADLINE = 86400; //In seconds
    const CONFIRMED = 1;
    const TENTATIVE = 0; //For beta purposes

    public static $defaults = array(
    		'date_id' => null,
    		'begin_datetime' => null,
    		'creator' => null,
    		'attendees' => array(),
    		'cancelled' => false,
    		'end_datetime' => null,
    		'summary' => null,
    		'location' => null,
    		'joined' => false,
    		'left' => false,
    		'status' => null,
    		'max_attendants' => 4
    );

	public function __construct(array $args = array()) {
		$defaults = self::$defaults;
		$args = array_merge($defaults, $args);

		//Assign the object properties
		$this->dbc = Database::connection();
		$this->date_id = $args['date_id'];
		$this->begin_datetime = $args['begin_datetime'];
		$this->end_datetime = $args['end_datetime'];
		$this->datename = $args['datename'];
		$this->creator = (is_a($args['creator'], 'Group') || is_a($args['creator'], 'User')) ? $args['creator'] : null;
		$this->attendees = (is_array($args['attendees'])) ? $args['attendees'] : array();
		$this->number_of_attendants = count($this->attendants);
		$this->cancelled = (!$args['cancelled']) ? true: false;
		$this->summary = $args['summary'];
		$this->location = $args['location'];
		$this->max_attendants = (is_numeric($args['max_attendants'])) ? $args['max_attendants'] : 4;
		if (!is_null($args['status'])) {
			if ($args['status'] == 0) {
				$this->left = true;
			}
			elseif ($args['status'] == 1){
				$this->joined = true;
			}
			else {
				throw new UnexpectedValueException('UnexpectedValueException occured on construction of object badminton date');
			}
		}

		$this->__resolve_db_type();
	}

    public static function beta_thread_to_date(Thread $thread) {
        /*
        (Thread) -> BadmintonDate/PublicProposedDate
        Beta function, attempts to record this thread into a possible date
         */
        if ($this->thread_id && $thread->is_tentative()) {
            $mysqli = Database::connection();
            $sql = "INSERT INTO `badminton_dates` (datename, begin_datetime, end_datetime) VALUES ('$thread->thread_title', '$thread->date_play', '$thread->date_play', '{$thread->author->user_id}')";
            $result = $mysqli->query($sql)
            or die ($mysqli->error);
            $date_id = $mysqli->insert_id;
            $sql = "UPDATE `threads` SET beta_date_id = '$date_id' WHERE thread_id = '$thread->thread_id' LIMIT 1";
            $result = $mysqli->query($sql)
            or die ($mysqli->error);
            $thread->beta_date_id = $date_id;
            return $thread;
        }
        else {
            throw new UnexpectedValueException('UnexpectedValueException occured on method call beta_thread_to_date');
        }
    }

	public function can_be_left() {
		/*
		(Null) -> Bool
		Returns whether or not the leave deadline is up
		 */
		if ($this->begin_datetime) {
			$time = strtotime($this->begin_datetime);
			return (($time - time()) > self::LEAVE_DEADLINE);
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on request, invalid parameters in method call `can_be_left`');
		}
	}

	public function get_datename() {
		/*
		(Null) -> Null
		 */
		if ($this->date_id) {
			$sql = "SELECT datename FROM `badminton_dates` WHERE date_id = '$this->date_id'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			if ($result->num_rows == 1) {
				$this->datename = $result->fetch_row()[0];
			}
			else {
				throw new OutOfRangeException('OutOfRangeException occured on method call get_datename, date does not exist');
			}
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call get_datename');
		}
	}

	public function get_times() {
		/*
		(Null) -> Null
		Attempts to create the property `begin_datetime` and `end_datetime`
		 */
		if ($this->date_id) {
			$sql = "SELECT begin_datetime, end_datetime FROM `badminton_dates` WHERE date_id = '$this->date_id'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			if ($result->num_rows == 1) {
				list($this->begin_datetime, $this->end_datetime) = mysqli_fetch_array($result, MYSQLI_NUM);
			}
			else {
				throw new OutOfRangeException('OutOfRangeException occured on method call get_times, date does not exist for date_id ' .$this->date_id);
			}
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on request, date_id is not valid');
		}
	}

	public function get_creator() {
		/*
		(Null) -> Null
		Attempts to create the property creator
		 */
		if ($this->date_id) {
			$sql = "SELECT t1.creator_id as `user_id, t2.username, t2.email, t2.reputation, t2.avatar
			FROM `badminton_dates` as t1 
			INNER JOIN `users` as t2 
			ON t2.user_id = t1.creator_id
			WHERE t1.date_id = '$this->date_id'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			if ($result->num_rows == 1) {
				$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
				$this->creator = new ProfileUser($row);
			}
			else {
				$this->creator = null;
			}
		}
		else {
			$this->creator = null;
		}
	}

	public function is_confirmed() {
		/*
		Attempts to check if the current date is confirmed or not
		 */
		if ($this->date_id) {
			$sql = "SELECT confirmed FROM `badminton_dates` WHERE date_id = '$this->date_id'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			return (mysqli_fetch_row($result)[0] == 1);
		}
		else {
			return false;
		}
	}

	public function get_attendees($users_to_exclude = array()) {
		/*
		(Null) -> Null
		Attempts to get all attendees
		 */
		if ($this->date_id) {
            $this->dbc = Database::connection();
			$sql = "SELECT t1.user_id, t2.username, t2.avatar, t2.reputation, t2.email
			FROM `joins` as t1
			INNER JOIN `users` as t2
			ON t2.user_id = t1.user_id
			WHERE t1.date_id = '$this->date_id'";
			foreach ($users_to_exclude as $user) {
				if (is_a($user, 'User')) {
					$sql .= ' AND t1.user_id != ' . $user->user_id;
				}
			}
			//echo $sql;
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			$this->attendees = array();
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$user = new User($row);
				array_push($this->attendees, $user);
			}
			//Set the number of attendees to match the attendeants
			$this->number_of_attendants = count($this->attendees);
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call get_attendees');
		}
	}

	protected function __resolve_db_type() {
		$class_name = get_class($this);
		switch ($class_name) {
			case 'PublicProposedDate':
			case 'GroupProposedDate':
				$this->db_type = UNCONFIRMED_DATE;
				break;
			case 'ConfirmedBadmintonDate':
				$this->db_type = CONFIRMED_DATE;
				break;
			case 'UnavailableDate':
				$this->db_type = UNAVAILABLE_DATE;
			default:
				throw new OutOfRangeException('OutOfRangeException when assignedthe db type to the object');
		}
	}
}


class SomeDate extends BadmintonDate {
	/*
	A generic class for some date, used so that dates can be modified without having to know if it is confirmed or not and thus use the corresponding class. Accepts the same injectable parameters as any other badminton date
	 */
}


abstract class ProposedDate extends BadmintonDate {
	/*
	A proposed date in either the public or a group that has not been confirmed yet
	 */
	
	public function confirm_date() {
		/*
		Confirms the current proposed date has been booked
		 */
		if ($this->date_id) {
			$sql = "UPDATE badminton_dates SET confirmed = 1 WHERE date_id = '$this->date_id'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			return true;
		}
		else {
			return false;
		}
	}
}


class UnavailableDate {
	/*
	Class for a date on the calendar that has already been confirmed to be booked but not claimed by anyone yet.
	 */
	
	private $dbc;
	public $date_id, $datetime, $datename;
}


class PublicProposedDate extends ProposedDate {
	/*
	Class for a generic proposed date that is public
	 */
}


class GroupProposedDate extends ProposedDate {
	/*
	Class for some group-proposed date that invites the whole group and can either be public or private
	 */
}


class ConfirmedBadmintonDate extends BadmintonDate {
	/*
	A date that has been confirmed to be booked already and has or has not happedn yet
	 */

	private $dbc;
	public $date_id, $datetime, $creator, $attendants, $datename;

    public static $defaults = array(
            'date_id' => null,
            'begin_datetime' => null,
            'creator' => null,
            'attendees' => array(),
            'cancelled' => false,
            'end_datetime' => null,
            'summary' => null,
            'location' => null,
            'joined' => false,
            'left' => false,
            'status' => null,
            'max_attendants' => 4
    );

	public function __construct(array $args = array()) {
		$defaults = self::$defaults;
		$args = array_merge($defaults, $args);

		//Assign the object properties
        $this->dbc = Database::connection();
		$this->date_id = $args['date_id'];
		$this->datetime = $args['datetime'];
		$this->creator = (is_a($args['creator'], 'Group') || is_a($args['creator'], 'User')) ? $args['creator'] : null;
		$this->attendants = $args['attendants'];
		$this->number_of_attendants = count($this->attendants);
        $this->begin_datetime = $args['begin_datetime'];
        $this->end_datetime = $args['end_datetime'];
        $this->datename = $args['datename'];
	}
}