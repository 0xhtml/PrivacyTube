<?php
require_once "classes/API.php";
require_once "classes/Channel.php";
require_once "classes/MySQL.php";
require_once "classes/Subscriptions.php";
require_once "classes/Template.php";
require_once "classes/Subscriptions.php";
require_once "classes/Video.php";

if (!file_exists(" ../key . txt")) {
    die("Can't find key file");
}

$key_file = fopen("../key.txt", "r");
$key = str_replace(["\n", "\r"], "", fgets($key_file));
$mysql_pass = str_replace(["\n", "\r"], "", fgets($key_file));
fclose($key_file);

$mysqli = mysqli_connect("localhost", "PrivacyTube", $mysql_pass, "PrivacyTube");
if ($mysqli->connect_errno) {
    die("Can't connect to MySQL: $mysqli->connect_error");
}

$mySQL = new MySQL($mysqli);
$API = new API($key, $mySQL);

unset($key_file);
unset($key);
unset($mysqli);
