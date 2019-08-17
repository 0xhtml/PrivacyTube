<?php
if (!isset($_GET["c"]) or strlen($_GET["c"]) != 24) {
    header("Location: .");
    die();
}

require_once "../classes/API.php";
require_once "../classes/Channel.php";
require_once "../classes/Config.php";
require_once "../classes/MySQL.php";
require_once "../classes/Template.php";
require_once "../classes/Video.php";
require_once "../classes/User.php";

$config = new Config();
$mySQL = new MySQL($config);
$API = new API($config, $mySQL);

$channel = Channel::fromId($_GET["c"], $API);

if (isset($_POST["subscribe"])) {
    $user = new User(true);
    $user->subscribe($channel, $mySQL);
} elseif (isset($_POST["unsubscribe"])) {
    $user = new User(true);
    $channel->unsubscribe($channel, $mySQL);
}

$video_preview_template = new Template("../templates/videoPreview.html");
$videos_html = "";

foreach (Video::fromChannel($channel, $API) as $video) {
    $video_preview_template->set_var("title", $video->getTitle());
    $video_preview_template->set_var("thumbnail", $video->getThumbnail());
    $video_preview_template->set_var("channel", $video->getChannel()->getName());
    $video_preview_template->set_var("channelId", $video->getChannel()->getId());
    $video_preview_template->set_var("id", $video->getId());
    $videos_html .= $video_preview_template->render();
}

$template = new Template("../templates/channel.html");
$template->set_var("id", $channel->getId());
$template->set_var("name", $channel->getName());
$template->set_var("subscribers", number_format($channel->getSubscribers()));
$template->set_var("image", $channel->getImage());
$template->set_var("videos", $videos_html);

$user = new User();
if ($user->getLoggedin() and $user->isSubscribed($channel, $mySQL)) {
    $template->set_var("action", "unsubscribe");
    $template->set_var("actionValue", "Unsubscribe");
} else {
    $template->set_var("action", "subscribe");
    $template->set_var("actionValue", "Subscribe");
}

$header_template = new Template("../templates/header.html");

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", $channel->getName() . " - PrivacyTube");
$page_template->set_var("header", $header_template->render());
$page_template->set_var("main", $template->render());

echo $page_template->render();
