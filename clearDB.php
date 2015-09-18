<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$mysqli = Database::connection();
$sql = "truncate actions;
truncate action_key_values;
truncate conversation_members;
truncate conversation_messages;
truncate conversation_requests;
truncate conversations;
truncate badminton_dates;
truncate joins;";
$result = $mysqli->multi_query($sql)
or die ($mysqli->error);
?>