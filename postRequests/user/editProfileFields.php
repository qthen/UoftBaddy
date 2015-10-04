<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$level = $data->level;
$program = $data->program;
$commuter = $data->commuter;
$accolades = $data->accolades;
$bio = $data->bio;
// $commuter = 2;
// $level = 'AngularJS Level';
// $program = 'Object Oriented';   
// $level = 1;
// $playingLevel = $data->playingLevel;
// $commuter = 1;
// $bio = 'hey there';
// $playingLevel = 1;  
$user = User::get_current_user(); //The user to edit on
try {
    if ($user instanceof CurrentUser) {
        if (is_numeric($level) && is_numeric($commuter)) {
            list($program, $accolades, $bio) = Database::sanitize(array($program, $accolades, $bio));
            $edit = new ProfileUser(array(
                'level' => $level,
                'program' => $program,
                'commuter' => $commuter,
                'bio' => $bio,
                'accolades' => $accolades)
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