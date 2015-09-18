<?php

/*
Think about groups creating private events and gorup events to only be shown to group members and maybe public groups like advanced intermaideate, or that engienering badminton club we once saw

 */

class Group {
	
	private $dbc;
	public $group_id, $group_name;

	public static $defaults = array(
		'group_id' => null,
		'group_name' => null,
		'group_description' => null
	);

	public function __construct(array $args = array()) {
		$defaults = self::$defaults;
		$args = array_merge($defaults, $args);

		//Assign the object properties
		$this->group_id = $args['group_id'];
		$this->group_name = $args['group_name'];
		$this->group_description = $args['group_description'];
	}
}
?>