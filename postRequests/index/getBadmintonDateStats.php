<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';

class IndexStats implements JsonSerializable {
	/*
	Class for the output of the stats
	*/

	const TOTAL_AVAILABLE_COURTS = 45;
	public $bookings_by_hour, $bookings_by_dates;

	public function __construct() {
		$keys = range(7, 24);
		$this->bookings_by_hour = array();
		for ($i = 0; $i < count($keys); $i++) {
			$this->bookings_by_hour[$keys[$i]] = 0;
		}	

		$this->bookings_by_dates = array();
	}

	public function jsonSerialize() {
		$bookings_by_dates = array();
		foreach ($this->bookings_by_dates as $key => $val) {
			if ($val == 0) {
				$vancancy_rate = 100;
			}	
			else {
				$vancancy_rate = round((intval($val) / intval(self::TOTAL_AVAILABLE_COURTS) * 100), 2) . '%';
			}
			$free_courts = intval(self::TOTAL_AVAILABLE_COURTS) - intval($val);
			$bookings_by_dates[] = array(
				'date' => $key,
				'bookings' => $val,
				'vancancy_rate' => $vancancy_rate,
				'free_courts' => $free_courts
			);
		}

		foreach ($this->bookings_by_hour as $key=>$val) {
			if (intval($key) > 12) {
				$time = $key - 12 . ':00 p.m';
			}
			else {
				$time = $key . ':00 a.m';
			}
			$bookings_by_hours[] = array(
				'time' => $time,
				'bookings' => $val
			);
		}
		return array(
			'bookings_by_hours' => $bookings_by_hours,
			'bookings_by_dates' => $bookings_by_dates 
		);
	}
}

//Create the new stats object
$returnStats = new IndexStats();

$mysqli = Database::connection();
$sql_get_badminton_date_stats = "SELECT DATE(t1.begin_datetime) as `date`, COUNT(t1.date_id) as `courts`
FROM `badminton_dates` as t1 
GROUP BY DATE(t1.begin_datetime)";
$result_get_badminton_date_stats = $mysqli->query($sql_get_badminton_date_stats)
or die ($mysqli->error);
while ($row = mysqli_fetch_array($result_get_badminton_date_stats, MYSQLI_ASSOC)) {
	$returnStats->bookings_by_dates[$row['date']] = $row['courts'];
}

$sql_get_most_free_timeslot = "SELECT (HOUR(t1.begin_datetime)) as `hour`, COUNT(t1.date_id) as `dates`
FROM `badminton_dates` as t1
GROUP BY HOUR(t1.begin_datetime)
ORDER BY hour";
$result_get_most_free_timeslot = $mysqli->query($sql_get_most_free_timeslot)
or die ($mysqli->error);
while ($row = mysqli_fetch_array($result_get_most_free_timeslot, MYSQLI_ASSOC)) {
	$returnStats->bookings_by_hour[$row['hour']] = $row['dates'];
}

echo json_encode($returnStats, JSON_PRETTY_PRINT);