<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request =file_get_contents('php://input');
$data = json_decode($request);
$user_id = $data->user_id;
try {
    if (is_numeric($user_id)) {
        $mysqli = Database::connection();
        $sql = "SELECT COUNT(join_id)
        FROM joins
        WHERE user_id = '$user_id'";
        $result = $mysqli->query($sql)
        or die ($mysqli->error);
        $joins = mysqli_fetch_row($result)[0];

        $sql = "SELECT COUNT(join_id)
        FROM joins
        WHERE user_id = '$user_id'
        AND status = '" . BadmintonDate::LEFT . "'";
        $result = $mysqli->query($sql)
        or die ($mysqli->error);
        $leaves = mysqli_fetch_row($result)[0];

        $sql = "SELECT COUNT(join_id)
        FROM joins
        WHERE user_id = '$user_id'
        AND status = '" . BadmintonDate::JOINED . "'";
        $result = $mysqli->query($sql)
        or die ($mysqli->error);
        $confirms = mysqli_fetch_row($result)[0];

        http_response_code(200);
        echo json_encode(array(
            'total_joins' => $joins,
            'leaves' => $leaves,
            'stays' => $confirms)
        , JSON_PRETTY_PRINT);
    }
}
catch (Exception $e) {
    http_response_code(400);
    Database::print_exception($e);
}