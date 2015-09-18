<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$user_id = $data->user_id;
//$user_id = 1069062649779910;
try {
    if (is_numeric($user_id)) {
        $user = new ProfileUser(array(
            'user_id' => $user_id)
        );
        $actions = ActionFactory::fetch_activity($user);
        http_response_code(200);
        echo json_encode($actions, JSON_PRETTY_PRINT);
    }
    else {
        throw new UnexpectedValueException('UnexpectedValueException occured on request');
    }
}
catch (Exception $e) {
    http_response_code(400);
    Database::print_exception($e);
}