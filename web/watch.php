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

$video_template = new Template("../templates/video.html");
$video_template->set_var("videoID", $video->get_id());
$video_template->set_var("videoTitle", $video->get_title());
$video_template->set_var("videoDescription", nl2br($video->get_description()));
$video_template->set_var("videoChannelName", $video->get_channel_name());
$video_template->set_var("videoViews", number_format($video->get_views()));
$video_template->set_var("videoLikes", number_format($video->get_likes()));
$video_template->set_var("videoDislikes", number_format($video->get_dislikes()));
$video_template->set_var("videoThumbnail", $video->get_thumbnail());
$video_template->set_var("videoDate", date(DATE_COOKIE, $video->get_date()));

$nav_template = new Template("../templates/nav.html");

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", $video->get_title() . " - PrivacyTube");

$page_template->set_var("nav", $nav_template->render());
$page_template->set_var("main", $video_template->render());
echo $page_template->render();
