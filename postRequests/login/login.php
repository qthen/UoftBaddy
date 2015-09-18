<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information
function __autoload($class_name) {
	/*
	Last chance for PHP script to call a class name
	 */
	$class_name = ($class_name == 'OutgoingTranslation' || $class_name == 'IncomingTranslation') ? 'Translate' : $class_name;
	require_once $_SERVER['DOCUMENT_ROOT']. "/includes/$class_name.php";
}
/*error_reporting(-1);*/
$request = file_get_contents('php://input');
$data = json_decode($request);
/*require_once '../../includes/database.php';
require_once '../../includes/user.php';*/
$email = $data->email;
$password = $data->password;
$rememberme = $data->rememberme;
/*$email = 'philippuihung.tsang@mail.utoronto.ca';
$password = 'haishdsa';*/
//$email = 'philippuihung.tsang@mail.utoronto.ca';
//$password = 'universa';

/*$username = 'User1';
$password = 'universa';*/

try {
/*	$return_val = array();
	$return_val['session'] = $_SESSION['user_id'];
	$return_val['token'] = $_COOKIE['token'];
	$return_val['cookie'] = $_COOKIE['user_id'];
	echo json_encode($return_val, JSON_PRETTY_PRINT);*/
	$user = User::get_current_user();
	if ($user instanceof CurrentUser) {
		//echo 'hi';
		http_response_code(200);
		echo json_encode($user, JSON_PRETTY_PRINT);
	}
	else {
		if ($email && $password) {
			$mysqli = Database::connection();
			$email = Database::sanitize($email);
			$sql = "SELECT user_id, password FROM users WHERE email = '$email'";
			$return_val[] = $sql;
			$result = $mysqli->query($sql)
			or die ($mysqli->error);

			$mysqli->set_charset('utf-8');

			if ($result->num_rows == 1) {
			//	echo 'hi';
				list($user_id, $hashed_password) = mysqli_fetch_row($result);
				$verify = password_verify($password, $hashed_password);
				if ($verify) {
					//The user is authenticated
					$size = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB);
					$new_token = bin2hex(mcrypt_create_iv($size, MCRYPT_DEV_RANDOM));
					$new_token_hashed = password_hash($new_token, PASSWORD_BCRYPT);

					//Insert new token into database4 hashed
					$sql = "UPDATE users SET token = '$new_token_hashed' WHERE user_id = '$user_id' LIMIT 1";
					$result = $mysqli->query($sql)
					or die ($mysqli->error);

					//Set the cookies based on rememberme
					if (!$rememberme) {
						setcookie('token', $new_token, time() + 6912000, "/");
						setcookie('user_id', $user_id, time() + 6912000, "/");
					}
					else {
						setcookie('token', $new_token, time() + 6912000, "/");
						setcookie('user_id', $user_id, time() + 6912000, "/");
					}
					$_COOKIE['user_id'] = $user_id;
					$_COOKIE['token'] = $new_token;
					$_SESSION['user_id'] = $user_id;

					$user = new CurrentUser(array(
						'user_id' => $user_id)
					);
					$user->log_login();

					http_response_code(200); ///Success
					echo json_encode($user, JSON_PRETTY_PRINT);
					/*echo 'succes';*/
					
				}
				else {
				/*	echo 'bad';*/
					throw new OutOfBoundsException('OutOfBoundsException raised on request');
				}
			}
			else {
				throw new OutOfRangeException('OutOfRangeException occured on request');
			}
		}
		else {
			throw new UnexpectedValueException('UnexpectedValueException rasied on request');
		}
	}
}
catch (UnexpectedValueException $e) {
	http_response_code(400);
	Database::print_exception($e);
}
catch (OutOfRangeException $e) {
	http_response_code(400);
	Database::print_exception($e);
}
catch (OutOfBoundsException $e) {
	http_response_code(400);
	Database::print_exception($e);
}