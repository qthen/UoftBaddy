<?php
class Thread {
	/*
	Class for some thread done on the general ste
	 */
	public static $defaults = array(
		'thread_id' => null,
		'thread_name' => null,
		'thread_title' => null,
		'author' => null,
		'comments' => array(),
		'date_posted' => null,
		'type' => 1,
		'date_play' => null
	);	

	public static $type_contracts = array(
		1 => 'Looking To Play', 
		2 => 'General'
	);

	const TENTATIVE = 1;

	const LOOKING_TO_PLAY = 1;
	const GENERAL_COMMENT = 2;

	public function __construct(array $args = array()) {
		$defaults = self::$defaults;
		$args = array_merge($defaults, $args);
		
		$this->dbc = Database::connection();
		$this->thread_text = $args['thread_text'];
		$this->thread_id = (is_numeric($args['thread_id'])) ? $args['thread_id'] : null;
		$this->author = (is_a($args['author'], 'User')) ? $args['author'] : null;
		$this->date_posted = $args['date_posted'];
		$this->comments = (is_array($args['comments'])) ? $args['comments'] : array();
		$this->thread_title = $args['thread_title'];
		$this->type = $args['type'];
		if (array_key_exists($this->type, self::$type_contracts)) {
			$this->str_type = self::$type_contracts[$this->type];
		}
		else {
			$this->str_type = null;
		}
		$this->date_play = $args['date_play'];
	}
	
	public function get_all_participants() {
		/*
		(Array of Users to Exclude) -> Array
		Based on this comment_id, attempts to get all the participants that have posed, should only be called on the parent comment
		 */
		if (!$this->parent) {
			$sql = "SELECT DISTINCT(t1.author_id) as `user_id`, t2.username, t2.reputation, t2.avatar, t2.avatar_link
			FROM (
				(
					SELECT t1.author_id, t1.thread_id
					FROM `thread_comments` as t1
				)
				UNION 
				(
					SELECT t1.author_id, t1.thread_id
					FROM `threads` as t1 
					WHERE t1.thread_id = '$this->thread_id'
				)
			) as t1 
			LEFT JOIN `users` as t2 
			ON t2.user_id = t1.author_id
			WHERE t1.thread_id = '$this->thread_id'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			$participants = array();
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$user = new ProfileUser($row);
				array_push($participants, $user);
			}
			return $participants;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call get_all_paticipants because the comment is not the parent');
		}
	}

	public function is_tentative() {
		/*
		(Null) -> Bool
		Attempts to return whether or not this date is a possible tentative badminton date that people can join
		 */
		if ($this->thread_id) {
			return $this->type == self::TENTATIVE;
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured in method call is_tentative because the thread_id is invalid');
		}
	}

	public function get_author() {
		/*
		(Null) -> Null
		Attempts to fetch the author object
		 */
		if ($this->thread_id) {
			$sql = "SELECT t1.author_id as `user_id`
			FROM threads as t1 
			WHERE t1.thread_id = '$this->thread_id'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			if ($result->num_rows == 1) {
				$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
				$author = new ProfileUser($row);
				$this->author = $author;
			}
			else {
				throw new OutOfRangeException('OutOfRangeException occured on method call get_author because thread does not exist');
			}
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call get_author because thread is not valid');
		}
	}
	public function get_comments() {
		/*
		(Null) -> Null
		Attempts to construct the property comments
		*/
		if ($this->thread_id) {
			$this->comments = array();
			$sql = "SELECT t1.comment_id, t1.comment_text, t1.author_id as `user_id`, t2.username, t1.date_posted, t2.avatar_link
			FROM `thread_comments` as t1 
			LEFT JOIN `users` as t2 
			ON t2.user_id = t1.author_id 
			WHERE t1.thread_id = '$this->thread_id'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$row['author'] = new ProfileUser(array(
					'user_id' => $row['user_id'],
					'username' => $row['username'],
					'avatar_link' => $row['avatar_link'],
					'date_posted' => $row['date_posted'])
				);
				$row['thread'] = clone $this;
				$thread_comment = new ThreadComment($row);
				array_push($this->comments, $thread_comment);
			}
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on method call get_comments because the thread id is invalid');
		}
	}
} 