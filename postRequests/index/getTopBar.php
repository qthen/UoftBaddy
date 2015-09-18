<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$mysqli = Database::connection();
$sql = "SELECT COUNT(date_id)
FROM `badminton_dates`
WHERE confirmed = 1
AND DATE(begin_datetime) = CURDATE()
";
$result = $mysqli->query($sql)
or die ($mysqli->error);
$row_confirmed = mysqli_fetch_row($result)[0];
$sql = "SELECT COUNT(*)
FROM `badminton_dates`
WHERE confirmed = 1
AND DATE(begin_datetime) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
";
$result = $mysqli->query($sql)
or die ($mysqli->error);
$row_confirmed_tomorrow = mysqli_fetch_row($result)[0];
$sql = "SELECT COUNT(thread_id)
FROM threads
WHERE WEEKOFYEAR(date_play) = WEEKOFYEAR(CURDATE())";
$result = $mysqli->query($sql)
or die ($mysqli->error);
$row_threads_thisweek = mysqli_fetch_row($result)[0];

//Figure out which dates are not full
$sql = "SELECT t1.max_attendants, t1.date_id, COALESCE(t2.attendees, 0), t1.max_attendants - t2.attendees as `space_available`
FROM `badminton_dates` as t1 
LEFT JOIN (
    SELECT t1.date_id, COUNT(t1.join_id) as `attendees`
    FROM `joins` as t1 
    WHERE t1.status = 1
    GROUP BY t1.date_id
) as t2 
ON t2.date_id = t1.date_id 
WHERE t2.attendees != t1.max_attendants
AND t1.confirmed = 1
AND DATE(begin_datetime) = CURDATE()
";
$result = $mysqli->query($sql)
or die ($mysqli->error);
$total_space = 0;
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $total_space += intval($row['space_available']);
}
$sql = "SELECT t1.max_attendants, t1.date_id, COALESCE(t2.attendees, 0), t1.max_attendants - t2.attendees as `space_available`
FROM `badminton_dates` as t1 
LEFT JOIN (
    SELECT t1.date_id, COUNT(t1.join_id) as `attendees`
    FROM `joins` as t1 
    WHERE t1.status = 1
    GROUP BY t1.date_id
) as t2 
ON t2.date_id = t1.date_id 
WHERE t2.attendees != t1.max_attendants
AND t1.confirmed = 1
AND DATE(begin_datetime) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
";
$result = $mysqli->query($sql)
or die ($mysqli->error);
$total_space_tomorrow = 0;
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $total_space_tomorrow += intval($row['space_available']);
}
$return_array = array(
    'confirmed_bookings_today' => $row_confirmed,
    'confirmed_bookings_tomorrow' => $row_confirmed_tomorrow,
    'total_space_today' => $total_space,
    'total_space_tomorrow' => $total_space_tomorrow,
    'looking_to_play_this_week' => $row_threads_thisweek
);
http_response_code(200);
echo json_encode($return_array, JSON_PRETTY_PRINT);