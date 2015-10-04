<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/vars/constants.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$user_id = $data->user_id;
$begin_datetime = $data->begin_datetime;
$datename = ($data->datename) ? $data->datename : DEFAULT_BADMINTON_DATE_NAME;
$end_datetime = $data->end_datetime;
$visibility = $data->visibility;
$summary = $data->summary;
$group_id = $data->group_id;
$user = User::get_current_user();
$max_attendants = $data->max_attendants;
/*$begin_datetime = '2015-09-06 12:00:00';
$end_datetime = '2015-09-06 13:00:00';*/
/*$begin_datetime = '2015-09-21 00:00:00';
$end_datetime = '2015-09-22 00:00:00';*/
try {
	if (($user instanceof CurrentUser) && $end_datetime && $begin_datetime) {
		//Attempt to create a new badminton date
		list($datetime, $datename, $summary) = Database::sanitize(array($datetime, $datename, $summary));
		if (!is_numeric($group_id))  {
			//The date is a public date
			$badminton_date = new PublicProposedDate(array(
				'summary' => $summary,
				'datename' => $datename,
				'begin_datetime' =>  $begin_datetime,
				'end_datetime' =>  $end_datetime,
				'creator' => $user,
				'max_attendants' => $max_attendants)
			);
		}
		else {
			//The date is a group date
			$group = new Group(array(
				'group_id' => $group_id)
			);
			$confirm = $user->in_group($group);
			if ($confirm) {
				$badminton_date = new GroupProposedDate();
			}
			else {
				throw new OutOfBoundsException('OutOfBoundsException occured, user is not in group');
			}
		}
		$create_result = $user->propose_badminton_date($badminton_date);
		if ($create_result) {
			http_response_code(200);
			echo json_encode($create_result, JSON_PRETTY_PRINT);
		}
		else {
			throw new RuntimeException('Runtime error occured, could not create the date for some reason');
		}
	}
	else {
		throw new UnexpectedValueException('UnexpectedValueException occured on request');
	}
}
catch (UnexpectedValueException $e) {
	http_response_code(400);
	Database::print_exception($e);
}
catch (RuntimeException $e) {
	http_response_code(400);
	Database::print_exception($e);
}
catch (OutOfBoundsException $e) {
	http_response_code(400);
	Database::print_exception($e);
}