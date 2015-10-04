<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
try {
    $mysqli = Database::connection();
    $user = User::get_current_user(); //To check if the user has joined in on this date
    $sql = "SELECT t1.date_id, t1.datename, t1.begin_datetime, t1.end_datetime
    FROM `athletic_centre` as t1 
    WHERE (WEEKDAY(t1.begin_datetime) BETWEEN 0 AND 6)
    AND (WEEKOFYEAR(
        IF(
            (WEEKDAY(CURDATE()) = 6), DATE_SUB(CURDATE(), INTERVAL 1 DAY), CURDATE())
        )
    ) = WEEKOFYEAR(t1.begin_datetime)";
    $result = $mysqli->query($sql)
    or die ($mysqli->error);
    $dates = array();
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $row['creator'] = new ProfileUser($row);
        $badminton_date = new ConfirmedBadmintonDate($row);
        array_push($dates, $badminton_date);
    }
    http_response_code(200);
    echo json_encode($dates, JSON_PRETTY_PRINT);
}
catch (Exception $e) {
    http_response_code(400);
    Database::print_exception($e);
}
?>