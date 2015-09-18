<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$thread_id = $data->thread_id;
$thread_title = $data->thread_title;
$thread_text = $data->thread_text;
/*$thread_id = 18;
$thread_text = 'editted text';*/
$user = User::get_current_user();
try {
    if (is_numeric($thread_id) && ($user instanceof User) && ($thread_text) && ($thread_title)) {
        list($thread_text, $thread_title) = Database::sanitize(array($thread_text, $thread_title));
        $editted_thread = new Thread(array(
            'thread_id' => $thread_id,
            'thread_text' => $thread_text,
            'thread_title' => $thread_title)
        );
        $result = $user->edit_thread($editted_thread);
        if ($result) {
            http_response_code(200);
            $result->thread_text = $data->thread_text;
            echo json_encode($result, JSON_PRETTY_PRINT);
        }
        else {
            throw new RuntimeException('RuntimeException occured on request, could not edit thread for some reason');
        }
    }
    else {
        throw new UnexpectedValueException('UnexpectedValueExceptionx occured on request');
    }
}
catch (Exception $e) {
    http_response_code(400);
    Database::print_exception($e);
}