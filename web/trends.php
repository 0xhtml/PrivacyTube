<?php
require_once "../classes/System.php";
require_once "../classes/Template.php";
require_once "../classes/User.php";
require_once "../classes/Video.php";

$system = new System();
$user = new User();

if ($user->getDonotdisturb($system)) {
    header("Location: .");
    die();
}

$video_preview_template = new Template("../templates/videoPreview.html");
$trends_html = "";

$lang = strtoupper(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
$lang = in_array($lang, System::API_REGIONS) ? $lang : "US";
foreach (Video::fromRegion($lang, $system) as $video) {
    $video_preview_template->set_var("title", $video->getTitle());
    $video_preview_template->set_var("thumbnail", $video->getThumbnail());
    $video_preview_template->set_var("channel", $video->getChannel()->getName());
    $video_preview_template->set_var("channelId", $video->getChannel()->getId());
    $video_preview_template->set_var("id", $video->getId());
    $trends_html .= $video_preview_template->render($user, $system);
}

$template = new Template("../templates/trends.html");
$template->set_var("trends", $trends_html, true);

$header_template = new Template("../templates/header.html");
if ($user->getLoggedin()) {
    $header_template->set_var("login", "logout");
    $header_template->set_var("loginl", "Logout");
} else {
    $header_template->set_var("login", "login");
    $header_template->set_var("loginl", "Login");
}

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", "Trends - PrivacyTube");
$page_template->set_var("header", $header_template->render($user, $system), true);
$page_template->set_var("main", $template->render($user, $system), true);

echo $page_template->render($user, $system);
