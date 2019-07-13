<?php
require_once "../classes/API.php";
require_once "../classes/Channel.php";
require_once "../classes/Config.php";
require_once "../classes/MySQL.php";
require_once "../classes/Search.php";
require_once "../classes/Template.php";
require_once "../classes/User.php";
require_once "../classes/Video.php";

$config = new Config();
$mySQL = new MySQL($config);
$API = new API($config, $mySQL);
$user = new User();

if (!isset($_GET["q"])) {
    header("Location: .");
    die();
}

$search = new Search($API, $mySQL, $_GET["q"]);

$video_preview_template = new Template("../templates/videoPreview.html");
$results_html = "";

foreach ($search->getResults(25) as $video) {
    $video_preview_template->set_var("title", $video->getTitle());
    $video_preview_template->set_var("thumbnail", $video->getThumbnail());
    $video_preview_template->set_var("channel", $video->getChannel()->getName());
    $video_preview_template->set_var("channelId", $video->getChannel()->getId());
    $video_preview_template->set_var("id", $video->getId());
    $results_html .= $video_preview_template->render();
}

$template = new Template("../templates/search.html");
$template->set_var("search", $_GET["q"]);
$template->set_var("results", $results_html);

$header_template = new Template("../templates/header.html");
$header_template->set_var("search", $_GET["q"]);

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", "Search - PrivacyTube");
$page_template->set_var("header", $header_template->render());
$page_template->set_var("main", $template->render());

echo $page_template->render();
