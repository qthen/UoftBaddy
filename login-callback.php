<?php
header("Content-type: text/html;charset=utf-8");
session_start();
//print_r($_SESSION);
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/Facebook/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/User.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/vars/constants.php';
//print_r($_SESSION);
//Create the Facebook service
$fb = new Facebook\Facebook ([
	'app_id' => '1632050047043531',
	'app_secret' => '265828cd94179b3a9f5e6e786cb90202',
	'default_graph_version' => 'v2.4'
	]);
$helper = $fb->getRedirectLoginHelper();
try {
	$accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
	// When Graph returns an error
	echo 'Graph returned an error: ' . $e->getMessage();
	exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
	// When validation fails or other local issues
	echo 'Facebook SDK returned an error: ' . $e->getMessage();
	exit;
}

if (isset($accessToken)) {
	$mysqli = Database::connection();

	// Logged in!
	$_SESSION['facebook_access_token'] = (string) $accessToken;

	//Now exchange the short lived access token for a longer lived access token
	$oAuth2Client = $fb->getOAuth2Client();
	$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);

	//Next get the user id of the current user, which is unique and will be used in the database
	// $url = 'https://graph.facebook.com/v2.4/me';
	// $fields = array(
	// 	'id', 
	// 	'name',
	// 	)
	$response = file_get_contents("https://graph.facebook.com/me?fields=id&access_token=$accessToken");
	//$response = http_get($url, )
	$data = json_decode($response);
	//print_r($data);
	$name = $data->name;
	$user_id = intval($data->id);

	//echo 'User id is ' . $user_id;

	$user = new CurrentUser(array(
		'user_id' => $user_id)
	);

	//var_dump($user);

	//Check to see if the user_id exists, if it doesn't then insert into the database
	if (!$user->user_exists()) {
		//Get the user's facebook info for the first time now
		$response = file_get_contents("https://graph.facebook.com/me?fields=id,name,cover&access_token=$accessToken");
		$data = json_decode($response);
		//var_dump($data);
		$name = $data->name;
		$cover = $data->cover->source;

		//Get the profile picture large to get the high resolution and for now record the url into the database
		$string = USER_PROFILE_PICTURE;
		$imageUrl = sprintf($string, $user->user_id);
		// $uniqueImageName = tempnam(AVATAR_DIR) . '.jpg';
		// $imageResource = imagecreatefromjpeg($uniqueImageName);
		$sql = "INSERT INTO `users` (user_id, username, avatar_link, date_registered, last_seen, cover) VALUES ($user_id, '$name', '$imageUrl', NOW(), NOW(), '$cover')";
		$result = $mysqli->query($sql)
		or die ($mysqli->error);
	}

	//Create the new tokens
	$size = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB);
	$new_token = bin2hex(mcrypt_create_iv($size, MCRYPT_DEV_RANDOM));
	$new_token_hashed = password_hash($new_token, PASSWORD_BCRYPT);

	$sql = "UPDATE `users` SET token = '$new_token_hashed' WHERE user_id = $user->user_id";
	//echo $sql;
	$result = $mysqli->query($sql)
	or die ($mysqli->error);

	//Now store the user id into the session variable
	$_SESSION['user_id'] = $user_id;
	setcookie('token', $new_token, time() + 6912000, "/");
	setcookie('user_id', $user_id, time() + 6912000, "/");
	$_COOKIE['user_id'] = $user_id;
	$_COOKIE['token'] = $new_token;
	$_SESSION['user_id'] = $user_id;

	$user->log_login();

	header('Location: http://uoftbaddy.ca'); //Go back to the home page with the authenticated user

	//Generate a new token 

	//For testing, insert this into the database

	//Give the user a token and store it in the token

	// //Now store the long lived access token into a hashed form and store that in the database
	// $hashed_access_token = password_hash($longLivedAccessToken, PASSWORD_BCRYPT);

	//Create the user id and session and cookie variables
	

	// Now you can redirect to another page and use the
	// access token from $_SESSION['facebook_access_token']
}
?>