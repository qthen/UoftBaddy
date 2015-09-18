<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
ini_set('memory_limit', '1024M'); 
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$mysqli = Database::connection();
$sql = "SELECT t1.thread_id, t1.thread_text, t1.thread_title, t1.date_posted, t1.author_id as `user_id`, t2.username, t2.email, t2.reputation, t2.avatar, t1.date_play as `date_play`
FROM `threads` as t1 
LEFT JOIN `users` as t2 
ON t2.user_id = t1.author_id
ORDER BY t1.date_posted DESC";
//echo $sql;
$result = $mysqli->query($sql)
or die ($mysqli->error);
$threads = array();
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	//echo 'logged';
    $row['author'] = new ProfileUser($row);
    $thread = new Thread($row);
    $thread->get_comments();
    array_push($threads, $thread);
}
http_response_code(200);
print_r($threads);	
//echo json_encode($threads, JSON_PRETTY_PRINT);