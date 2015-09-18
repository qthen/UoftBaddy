<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
function __autoload($class) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/$class.php";
}
$request = file_get_contents('php://input');
$data = json_decode($request);
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
	if (!$user->user_id) {
		//Check input fields
		if ((strlen($password) >= 4) && ($confirm_password == $password) && (strlen($username) >= 2)) {
			$mysqli = Database::connection();

			//Sanitize user inputs
			list($password, $confirm_password, $username, $email) = Database::sanitize(array($password, $confirm_password, $username, $email));

			//Check if this email is not in the database
			if (!User::email_exists($email)) {
				$token = User::generate_token();
				$hashed_token = password_hash($token, PASSWORD_BCRYPT);

				$hashed_password = password_hash($password, PASSWORD_BCRYPT);

				$sql = "INSERT INTO users (email, password, type, token) VALUES ('$email', '$hashed_password', '0', '$hashed_token')";
				$result = $mysqli->query($sql)
				or die ($mysqli->error);

				$user_id = $mysqli->insert_id;

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
				throw new UnexpectedValueException('UnexpectedValueException occured on request');
			}
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException occured on request');
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