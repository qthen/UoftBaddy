<?php
class Renderer {
	/*
	Class for rendering HTML views
	 */
	public static function get_sidebar() {
		/*
		Gets the sidebar 
		 */
		require_once $_SERVER['DOCUMENT_ROOT'] . '/ui/sidebar.php';
	}
	public static function get_user_dropdown() {
		/*
		Gets the user dropdown
		*/
		require_once $_SERVER['DOCUMENT_ROOT'] . '/ui/userDropdown.php';
	}
}