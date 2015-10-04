<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$user = User::get_current_user();
try {
    $mysqli = Database::connection();
    $user = User::get_current_user(); //To check if the user has joined in on this date
    $sql = "SELECT t1.date_id, t1.datename, t1.begin_datetime, t1.end_datetime, t1.confirmed, t1.bool_group, t2.username, t2.email, t2.reputation, t2.avatar, t1.creator_id as `user_id`
    FROM `badminton_dates` as t1 
    LEFT JOIN users as t2
    ON t2.user_id = t1.creator_id
    WHERE t1.confirmed = '" . BadmintonDate::CONFIRMED . "'
    AND DATE(t1.begin_datetime) = CURDATE() OR DATE(t1.begin_datetime) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
    $result = $mysqli->query($sql)
    or die ($mysqli->error);
    $dates = array();
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $row['creator'] = new ProfileUser($row);
        $badminton_date = new ConfirmedBadmintonDate($row);
        $badminton_date->get_all_joins(); //Get's also if it is full or not
        //Now check if the user is in the attendness
        if ($user instanceof CurrentUser) {
            for ($i = 0; $i < count($badminton_date->joins); $i++) {
                if ($badminton_date->joins[$i]->user_id == $user->user_id) {
                    if ($badminton_date->joins[$i]->absent) {
                        $badminton_date->absent = true;
                        $badminton_date->joined = true;
                    }
                    else {
                        $badminton_date->joined = true;
                        $badminton_date->absent = false;
                    }
                    break;
                }
                else {
                    $badminton_date->joined = false;
                    $badminton_date->absent = false;
                }
            }
        }
        $badminton_date->get_attendees(); 
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