<?php
require_once "../classes/API.php";
require_once "../classes/Config.php";
require_once "../classes/MySQL.php";
require_once "../classes/Template.php";
require_once "../classes/Video.php";

$config = new Config();
$mySQL = new MySQL($config);
$API = new API($config, $mySQL);

$video_preview_template = new Template("../templates/videoPreview.html");
$trends_html = "";

$lang = strtoupper(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
$lang = in_array($lang, $API::REGIONS) ? $lang : "US";
foreach (Video::fromRegion($API, $lang) as $video) {
    $video_preview_template->set_var("title", $video->getTitle());
    $video_preview_template->set_var("thumbnail", $video->getThumbnail());
    $video_preview_template->set_var("channel", $video->getChannel()->getName());
    $video_preview_template->set_var("channelId", $video->getChannel()->getId());
    $video_preview_template->set_var("id", $video->getId());
    $trends_html .= $video_preview_template->render();
}

$template = new Template("../templates/trends.html");
$template->set_var("trends", $trends_html);

$header_template = new Template("../templates/header.html");

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", "Trends - PrivacyTube");
$page_template->set_var("header", $header_template->render());
$page_template->set_var("main", $template->render());

echo $page_template->render();
