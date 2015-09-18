<?php
class ReputationChange {
	public $reputation_change, $reason;

	public static $defaults = array(
		'reputation_change' => 0,
		'reason' => null
	);

	public function __construct(array $args = array()) {
		$defaults = self::$defaults;
		$args = array_merge($defaults, $args);

		//Assign the object properities
		$this->reputation_change = (is_numeric($args['reputation_change'])) ? $args['reputation_change'] : null;
		$this->reason = $args['reason'];
	}
}