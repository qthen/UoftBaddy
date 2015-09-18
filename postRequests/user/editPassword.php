<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$password = $data->password;
$confirm = $data->confirm_password;
$user = User::get_current_user();
try {
    if ($user instanceof CurrentUser) {
        if ($password == $confirm) {
            $mysqli = Database::connection();
            $password = Database::sanitize($password);
            $sql = "UPDATE users SET password = '$password' WHERE user_id = '$user->user_id' LIMIT 1";
            $result = $mysqli->query($sql)
            or die ($mysqli->error);
            http_response_code(200);
        }
        else {
            throw new UnexpectedValueException('Passwords do not match');
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