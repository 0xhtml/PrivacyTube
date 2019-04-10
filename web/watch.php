<?php
include_once "../init.php";

if (!isset($_GET["v"]) or strlen($_GET["v"]) != 11) {
    header("Location: .");
    die();
}

$video = $API->getVideo($_GET["v"]);

$channel = $API->getChannel($video->getChannelId());

$template = new Template("../templates/watch.html");
$template->set_var("videoID", $video->getId());
$template->set_var("videoTitle", $video->getTitle());
$template->set_var("videoDescription", nl2br($video->getDescription()));
$template->set_var("videoViews", number_format($video->getViews()));
$template->set_var("videoLikes", number_format($video->getLikes()));
$template->set_var("videoDislikes", number_format($video->getDislikes()));
$template->set_var("videoDate", date("d. M Y H:s", $video->getDate()));

$template->set_var("channelId", $channel->getId());
$template->set_var("channelName", $channel->getName());
$template->set_var("channelImage", $channel->getImage());
$template->set_var("channelSubscribers", number_format($channel->getSubscribers()));

$header_template = new Template("../templates/header.html");

$nav_template = new Template("../templates/nav.html");

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", $video->getTitle() . " - PrivacyTube");
$page_template->set_var("header", $header_template->render());
$page_template->set_var("nav", $nav_template->render());
$page_template->set_var("main", $template->render());

echo $page_template->render();
