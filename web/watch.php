<?php
require_once "../classes/System.php";
require_once "../classes/Template.php";
require_once "../classes/User.php";
require_once "../classes/Video.php";

if (!isset($_GET["v"]) or strlen($_GET["v"]) != 11) {
    header("Location: .");
    die();
}

$system = new System();
$user = new User();

$video = Video::fromId($_GET["v"], $system);

$template = new Template("../templates/watch.html");
$template->set_var("videoId", $video->getId());
$template->set_var("videoSrc", $video->getVideoSrc()->getHtml(), true);
$template->set_var("videoTitle", $video->getTitle());
$template->set_var("videoDescription", nl2br(htmlspecialchars($video->getDescription())), true);
$template->set_var("videoDate", date("d. M Y H:s", $video->getDate()));

$template->set_var("channelId", $video->getChannel()->getId());
$template->set_var("channelName", $video->getChannel()->getName());
$template->set_var("channelImage", $video->getChannel()->getImage());

$header_template = new Template("../templates/header.html");
if ($user->getLoggedin()) {
    $header_template->set_var("login", "logout");
    $header_template->set_var("loginl", "Logout");
} else {
    $header_template->set_var("login", "login");
    $header_template->set_var("loginl", "Login");
}

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", $video->getTitle() . " - PrivacyTube");
$page_template->set_var("header", $header_template->render($user, $system), true);
$page_template->set_var("main", $template->render($user, $system), true);

echo $page_template->render($user, $system);
