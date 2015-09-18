<?php
class ActionFrame {
	//Class for setting up all the required files for the actions

	public static function setUp() {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/Actions.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/SpecificActions.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/ActionFactory.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/ActionPusher.php';
	}
}
?>