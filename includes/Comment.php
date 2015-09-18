<?php
/*require_once __DIR__ . '/database.php';*/


abstract class Comment {
	/*
	Abstract class for a comment
	 */

	public function self_destruct() {
		/*
		(Null) -> Null
		Deletes self from database
		 */
		
		if ($this->comment_id) {
			if (get_class($this) == 'ProfileComment') {
				$sql = "DELETE FROM `profile_comments` WHERE comment_id = '$this->comment_id' LIMIT 1";
				$result = $this->dbc->query($sql)
				or die ($this->dbc->error);
				return true;
			}
			elseif (get_class($this) == 'GeneralComment') {
				$sql = "DELETE FROM `general_comments` WHERE comment_id = '$this->comment_id' LIMIT 1";
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


class ThreadComment extends Comment {
	/*
	Comment on a thread on the site
	 */
	private $dbc;
	public $comment_id, $comment_text, $author, $date_posted, $thread, $parent;

	public static $defaults = array(
		'comment_id' => null,
		'comment_text' => null,
		'author' => null,
		'date_posted' => null,
		'parent' => null,
		'thread' => null
	);

	public function __construct(array $args) {
		$defaults = self::$defaults;
		$args = array_merge($defaults, $args);

		$this->dbc = Database::connection();
		$this->comment_id = $args['comment_id'];
		$this->comment_text = $args['comment_text'];
		$this->author = (is_a($args['author'], 'User')) ? $args['author'] : null;
		$this->date_posted = $args['date_posted'];
		$this->parent = (is_a($args['parent'], 'ThreadComment')) ? $args['parent'] : null;
		if ($this->parent) {
			$this->reply = true;
		}
		else {
			$this->reply = false;
		}
		$this->thread = (is_a($args['thread'], 'Thread')) ? $args['thread'] : null;
	}

	public function get_replies() {
		/*
		(Null) -> Null
		Attempts to fetch all comments to this comment
		 */
		if ($this->comment_id) {
			$sql = "SELECT ";
		}
	}
	
	public function increment_likes() {
		/*
		Attempts to increment likes
		 */
		if ($this->comment_id) {
			$sql = "UPDATE `general_comments` SET likes = likes + 1 WHERE comment_id = '$this->comment_id'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			return true;
		}
		else {
			return false;
		}
	}

	public function decrement_likes() {
		/*
		Attempts to increment likes
		 */
		if ($this->comment_id) {
			$sql = "UPDATE `general_comments` SET likes = likes - 1 WHERE comment_id = '$this->comment_id'";
			$result = $this->dbc->query($sql)
			or die ($this->dbc->error);
			return true;
		}
		else {
			return false;
		}
	}

}

class ProfileComment extends Comment {
	private $dbc;
	public $comment_id, $comment_text, $author, $date_posted, $profile, $likes;

	public static $defaults = array(
		'comment_id' => 0,
		'comment_text' => null,
		'author' => null,
		'date_posted' => null,
		'profile' => null
	);

	public function __construct(array $args = array()) {
		$defaults = self::$defaults;
		$args = array_merge($defaults, $args);

		$this->dbc = Database::connection();
		$this->comment_id = (is_numeric($args['comment_id'])) ? $args['comment_id'] : null;
		$this->comment_text = $args['comment_text'];
		$this->date_posted = $args['date_posted'];
		$this->author = (is_a($args['author'], 'User')) ? $args['author'] : null;
		$this->profile = (is_a($args['profile'], 'User')) ? $args['profile'] : null;
		$this->likes = (is_numeric($args['likes'])) ? $args['likes'] : 0;
	}
}