<?php
require_once "../classes/API.php";
require_once "../classes/Config.php";
require_once "../classes/MySQL.php";
require_once "../classes/Template.php";
require_once "../classes/Video.php";
//require_once "../classes/VideoProgress.php";

if (!isset($_GET["v"]) or strlen($_GET["v"]) != 11) {
    header("Location: .");
    die();
}

$config = new Config();
$mySQL = new MySQL($config);
$API = new API($config, $mySQL);

$video = Video::fromId($_GET["v"], $API);
//$videoProgress = new VideoProgress($mySQL, $video, $user);

/*$video_preview_template = new Template("../templates/videoPreview.html");
$related_html = "";

foreach ($video->getRelatedVideos(10) as $relatedVideo) {
    $video_preview_template->set_var("title", $relatedVideo->getTitle());
    $video_preview_template->set_var("thumbnail", $relatedVideo->getThumbnail());
    $video_preview_template->set_var("channel", $relatedVideo->getChannel()->getName());
    $video_preview_template->set_var("channelId", $relatedVideo->getChannel()->getId());
    $video_preview_template->set_var("id", $relatedVideo->getId());
    $related_html .= $video_preview_template->render();
}*/

$template = new Template("../templates/watch.html");
$template->set_var("videoId", $video->getId());
$template->set_var("videoSrc", $video->getVideoSrc()->getSrc());
$template->set_var("videoTitle", $video->getTitle());
$template->set_var("videoDescription", nl2br($video->getDescription()));
$template->set_var("videoViews", number_format($video->getViews()));
$template->set_var("videoLikes", number_format($video->getLikes()));
$template->set_var("videoDislikes", number_format($video->getDislikes()));
$template->set_var("videoDate", date("d. M Y H:s", $video->getDate()));

$template->set_var("channelId", $video->getChannel()->getId());
$template->set_var("channelName", $video->getChannel()->getName());
$template->set_var("channelImage", $video->getChannel()->getImage());
$template->set_var("channelSubscribers", number_format($video->getChannel()->getSubscribers()));

//$template->set_var("progress", $videoProgress->getProgress());

//$template->set_var("related", $related_html);

$header_template = new Template("../templates/header.html");

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", $video->getTitle() . " - PrivacyTube");
$page_template->set_var("header", $header_template->render());
$page_template->set_var("main", $template->render());

echo $page_template->render();
