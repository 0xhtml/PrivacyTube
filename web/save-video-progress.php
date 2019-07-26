<?php
require_once "../classes/API.php";
require_once "../classes/Channel.php";
require_once "../classes/Config.php";
require_once "../classes/MySQL.php";
require_once "../classes/User.php";
require_once "../classes/Video.php";
require_once "../classes/VideoProgress.php";

if (!isset($_GET["p"]) or !is_numeric($_GET["p"]) or !isset($_GET["v"]) or strlen($_GET["v"]) != 11) {
    die();
}

$config = new Config();
$mySQL = new MySQL($config);
$API = new API($config, $mySQL);
$user = new User();
$video = new Video($API, $mySQL, $_GET["v"], "", "", "", new Channel($API, $mySQL, "", "", "", 0, ""), 0, 0, 0, 0, "");
$videoProgress = new VideoProgress($mySQL, $video, $user);

$p = floatval($_GET["p"]);
$videoProgress->setProgress($p);
