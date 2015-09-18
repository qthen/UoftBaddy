<?php
header("Content-Type: application/json"); //Set header for outputing the JSON information 
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$actions = ActionFactory::fetch_sitewide_activity();
http_response_code(200);
echo json_encode($actions, JSON_PRETTY_PRINT);
?>