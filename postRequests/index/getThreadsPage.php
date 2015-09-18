<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
ini_set('memory_limit', '1024M'); 
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$mysqli = Database::connection();
$sql = "SELECT t1.thread_id, t1.thread_text, t1.thread_title, t1.date_posted, t1.author_id as `user_id`, t2.username, t2.email, t2.reputation, t2.avatar, DATE(t1.date_play) as `date_play`, t2.avatar_link, t1.type
FROM `threads` as t1 
LEFT JOIN `users` as t2 
ON t2.user_id = t1.author_id
LEFT JOIN (
	SELECT thread_id, MAX(date_posted) as `recent_activity`
	FROM `thread_comments`
	GROUP BY thread_id
) as t3
ON t3.thread_id = t1.thread_id
ORDER BY GREATEST(t1.date_posted, COALESCE(t1.date_posted, t3.recent_activity)) DESC";
//echo $sql;
$result = $mysqli->query($sql)
or die ($mysqli->error);
$threads = array();
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $row['author'] = new ProfileUser($row);
    $thread = new Thread($row);
    //$thread->get_comments();
    array_push($threads, $thread);
}
http_response_code(200);
echo json_encode($threads, JSON_PRETTY_PRINT);