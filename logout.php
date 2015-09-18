<?php
session_start();
unset($_SESSION['user_id']);
$_COOKIE['token'] = null;
$_COOKIE['user_id'] = null;
setcookie('user_id', null, time() - 3600, "/");
setcookie('token', null, time() - 3600, "/");
session_destroy();
header('Location:: http://uoftbaddy.ca');
?>