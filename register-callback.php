<?php
function __autoload($class) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/$class.php";
}
try {
	$user = User::get_current_user();
	$mysqli = Database::connection();
	if (!$user->user_id) {
		if (isset($_GET['token']) && (is_numeric($_GET['id']))) {
			$token = Database::sanitize($_GET['token']);
			$user_id = $_GET['id'];

			//Check to see if this is the correct token
			$sql = "SELECT email_token FROM users WHERE user_id = '$user_id' AND type != 1";
			$result = $mysqli->query($sql)
			or die ($mysqli->error);

			if ($result->num_rows == 1) {
				$token_hashed = mysqli_fetch_row($result)[0];
				if (password_verify($token, $token_hashed)) {
					//Officiate the user
					$sql = "UPDATE `users` SET type = 1 WHERE user_id ='$user_id'";
					$result = $mysqli->query($sql)
					or die ($mysqli->error);

					//Create a real token and handshake with user
					$token = User::generate_token();
					$hashed_token = Database::sanitize(password_hash($token, PASSWORD_BCRYPT));

					//Send the hashed token to the server
					$sql = "UPDATE `users` SET token = '$hashed_token'";
					$result = $mysqli->query($sql)
					or die ($mysqli->error);

					//Pass to cookies
					$_SESSION['user_id'] = $user_id;
					setcookie('user_id', $user_id, time() + 3600, "/");
					setcookie('token', $token, time() + 3600, "/");
					$_COOKIE['user_id'] = $user_id;
					$_COOKIE['token'] = $token;
					echo 'Account verified';
				}
				else {
					throw new OutOfBoundsException;
				}
			}
			else {
				throw new OutOfRangeException;
			}
		}
		else {
			throw new UnexpectedValueException;
		}
	}
	else {
		throw new UnexpectedValueException('UnexpectedValueException occured on request');
	}
}
catch (Exception $e) {
	header('Location: 404.php');
}

?>