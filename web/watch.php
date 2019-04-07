<?php
include_once "../init.php";

if (!isset($_GET["v"]) or strlen($_GET["v"]) != 11) {
    header("Location: .");
    die();
}

$video = new APIVideo($API, $_GET["v"]);

if ($video->error) {
    header("Location: .");
    die();
}

$channel = new APIChannel($API, $video->get_channel_id());

if ($channel->error) {
    header("Location: .");
    die();
}

$template = new Template("../templates/watch.html");
$template->set_var("videoID", $video->get_id());
$template->set_var("videoTitle", $video->get_title());
$template->set_var("videoDescription", nl2br($video->get_description()));
$template->set_var("videoViews", number_format($video->get_views()));
$template->set_var("videoLikes", number_format($video->get_likes()));
$template->set_var("videoDislikes", number_format($video->get_dislikes()));
$template->set_var("videoDate", date("d. M Y H:s", $video->get_date()));
$template->set_var("channelTitle", $channel->get_title());
$template->set_var("channelImage", $channel->get_image());
$template->set_var("channelSubscribers", number_format($channel->get_subscribers()));

$header_template = new Template("../templates/header.html");

$nav_template = new Template("../templates/nav.html");

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", $video->get_title() . " - PrivacyTube");
$page_template->set_var("header", $header_template->render());
$page_template->set_var("nav", $nav_template->render());
$page_template->set_var("main", $template->render());

echo $page_template->render();
