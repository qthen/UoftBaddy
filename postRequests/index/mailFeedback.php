<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$username = $data->username;
$user_id = $data->user_id;
$message = $data->message;
$user = User::get_current_user();
try {
	if ($message && $user instanceof CurrentUser) {
		$fullMessage = "From user: $username <br/> $message";
		$to = 'philiptsang018@gmail.com';
		$subject = 'UoftBaddy';
		$headers.="MIME-Version: 1.0 \r\n";
		$headers.="Content-type: text/html; charset=\"UTF-8\" \r\n";
		mail($to, $subect, $message, $headers);
		http_response_code(200);

	}
	else {
		throw new UnexpectedValueException('UnexpectedValueException occured on request since the input parameters are invalid');
	}
}
catch (UnexpectedValueException $e) {
	http_response_code(400);
	Database::print_exception($e);
}