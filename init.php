<?php
require_once "classes/Template.php";

require_once "classes/API/API.php";
require_once "classes/API/APISubscriptions.php";
require_once "classes/API/APIChannel.php";
require_once "classes/API/APIOAuth.php";
require_once "classes/API/APIVideo.php";

$key_file = fopen("../key.txt", "r");
$key = str_replace(["\n", "\r"], "", fgets($key_file));
$client_id = str_replace(["\n", "\r"], "", fgets($key_file));
$client_secret = str_replace(["\n", "\r"], "", fgets($key_file));
$mysql_pass = str_replace(["\n", "\r"], "", fgets($key_file));
fclose($key_file);
$API = new API($key, mysqli_connect("localhost", "PrivacyTube", $mysql_pass, "PrivacyTube"));
