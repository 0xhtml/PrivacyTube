<?php
require_once "../classes/API.php";
require_once "../classes/Config.php";
require_once "../classes/MySQL.php";
require_once "../classes/Template.php";
require_once "../classes/User.php";
require_once "../classes/Video.php";

$config = new Config();
$mySQL = new MySQL($config);
$API = new API($config, $mySQL);
$user = new User(true);

$video_preview_template = new Template("../templates/videoPreview.html");
$subscriptions_html = "";

foreach (Video::fromUser($user, $API, $mySQL) as $video) {
    $video_preview_template->set_var("title", $video->getTitle());
    $video_preview_template->set_var("thumbnail", $video->getThumbnail());
    $video_preview_template->set_var("channel", $video->getChannel()->getName());
    $video_preview_template->set_var("channelId", $video->getChannel()->getId());
    $video_preview_template->set_var("id", $video->getId());
    $subscriptions_html .= $video_preview_template->render();
}

$template = new Template("../templates/subscriptions.html");
$template->set_var("subscriptions", $subscriptions_html);

$header_template = new Template("../templates/header.html");

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", "Subscriptions - PrivacyTube");
$page_template->set_var("header", $header_template->render());
$page_template->set_var("main", $template->render());

echo $page_template->render();
