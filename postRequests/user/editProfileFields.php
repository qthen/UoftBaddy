<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$level = $data->level;
$program = $data->program;
$commuter = $data->commuter;
$user = User::get_current_user(); //The user to edit on
try {
    if ($user instanceof User) {
        if (is_numeric($level) && is_numeric($commuter)) {
            $program = Database::sanitize($program);
            $edit = new ProfileUser(array(
                'level' => $level,
                'program' => $program,
                'commuter' => $commuter)
            );
            $result = $user->edit_self($edit);
            if ($result) {
                http_response_code(200);
            }
            else {
                throw new RuntimeException('RuntimeException occured on request, could not edit for some reason');
            }
        }
        else {
            throw new UnexpectedValueException('UnexpectedValueException occured on request');
        }
    }
    else {
        throw new UnexpectedValueException('UnexpectedValueException occured on request, no user is currently logged in');
    }
}
catch (Exception $e) {
    http_response_code(400);
    Database::print_exception($e);
}