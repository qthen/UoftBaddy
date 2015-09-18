<?php
$mysqli = Database::connection();
$sql = "SELECT date_id, datename, begin_datetime, end_datetime 
FROM `badminton_dates`
WHERE";
$result = $mysqli->query($sql)
or die ($mysqli->error);
?>