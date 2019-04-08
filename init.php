<?php
require_once "classes/Template.php";

require_once "classes/API/API.php";
require_once "classes/API/APIChannel.php";
require_once "classes/API/APIOAuth.php";
require_once "classes/API/APIVideo.php";

$key_file = fopen("../key.txt", "r");
$key = fgets($key_file);
$client_id = fgets($key_file);
$client_secret = fgets($key_file);
fclose($key_file);
$API = new API($key);
