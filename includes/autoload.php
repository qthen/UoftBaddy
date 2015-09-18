<?php
function __autoload($class) {
	$actions = array(
		'JoinBadmintonDate', 'ProposeBadmintonDate', 'LeaveBadmintonDate', 'JoinDiscussion', 'ActionFactory', 'ActionPusher', 'PostedThread', 'PostedCommentOnThread', 'SiteAction');	
	$notifications = array(
		'NotificationPusher', 'NotificationFactory');
	if (in_array($class, $actions)) {
		//echo $class;
		require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/ActionFrame.php';
		ActionFrame::setUp();
	}
	else {
		if(in_array($class, $notifications)) {
			$class = 'Notifications';
		}
		if ($class == 'PendingEndorsementToken') {
			$class = 'PendingActions';
		}
		if ($class == 'ProfileUser') {
			$class = 'User';
		}
		if (($class == 'PublicProposedDate') || ($class == 'ConfirmedBadmintonDate')) {
			$class = 'BadmintonDate';
		}
		if (($class == 'Conversation') || ($class == 'ConversationFactory') || ($class == 'Message')) {
			$class = 'WebMessage';
		}
		if (in_array($class, $notifications)) {
			$class = 'Notifications';
		}
		if ($class == 'ThreadComment') {
			$class = 'Comment';
		}
		require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/$class.php";
	}
}
?>