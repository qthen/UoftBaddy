<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$request = file_get_contents('php://input');
$data = json_decode($request);
$user = User::get_current_user();
$username = $data->username;
$password = $data->password;
$email = $data->email;
$confirm_password = $data->confirm_password;
/*$email = 'philippuihung.tsang@mail.utoronto.ca';
$username = 'Philip';
$password = 'universa';
$confirm_password = 'universa';*/
$user = User::get_current_user();
try {
	if ($user instanceof User) {
		if (!$user->is_confirmed()) {
			//the user is not confirmed and is requesting confirmation, approve the request
			$email_token = User::generate_token();
			$hashed_email_token = password_hash($token, PASSWORD_BCRYPT);

			//Mail the user the email
			$subject = 'Verify UofT Baddy account';
			$message = "
			<html>
				<body>
					<p>
					Click <a href=\"http://uoftbaddy.ca/register-callback.php?id=$user_id&token=$token\">here</a> to verify your account
					</p>
				</body>
			</html>";
			$headers = "Content-Type: text/html; charset=ISO-8859-1\r\n";
			mail($email, $subject, $message, $headers);
			http_response_code(200);
		}
		else {
			throw new RuntimeException('RuntimeException occured on request, the user is already confirmed');
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