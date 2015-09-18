<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$user = User::get_current_user();
$request = file_get_contents('php://input');
$data = json_decode($request);
$thread_id = $data->thread_id;
try {
    if (is_numeric($thread_id)) {
        $mysqli = Database::connection();
        $sql = "SELECT t1.thread_id, t1.thread_title, t1.thread_text, t1.date_play, t1.date_posted, t1.author_id as `user_id`, t2.username, t2.email, t2.reputation, t2.avatar
        FROM `threads` as t1 
        LEFT JOIN `users` as t2 
        ON t2.user_id = t1.author_id
        WHERE t1.thread_id = '$thread_id'
        AND t1.type = '" . Thread::LOOKING_TO_PLAY . "'";
        $result = $mysqli->query($sql)
        or die ($mysqli->error);
        if ($result->num_rows == 1) {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $row['author'] = new ProfileUser($row);
            $thread = new Thread($row);
            http_response_code(200);
            echo json_encode($thread, JSON_PRETTY_PRINT);
        }
        else {
            throw new OutOfRangeException('OutOfRangeException occured on request, the thread does not exist');
        }
    }
    else {
        throw new UnexpectedValueException('UnexpectedValueException occured on request');
    }
}
catch (Exception $e) {
    http_response_code(400);
    Database::print_exception($e);
}