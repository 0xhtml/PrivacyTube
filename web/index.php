<?php
require_once "../classes/System.php";
require_once "../classes/Template.php";
require_once "../classes/User.php";
require_once "../classes/Video.php";

$system = new System();
$user = new User();

$video_preview_template = new Template("../templates/videoPreview.html");
if ($user->getLoggedin()) {
    $subscriptions_html = "<div class=\"videos\">";
    foreach (Video::fromUser($user, $system, 5) as $video) {
        $video_preview_template->set_var("title", $video->getTitle());
        $video_preview_template->set_var("thumbnail", $video->getThumbnail());
        $video_preview_template->set_var("channel", $video->getChannel()->getName());
        $video_preview_template->set_var("channelId", $video->getChannel()->getId());
        $video_preview_template->set_var("id", $video->getId());
        $subscriptions_html .= $video_preview_template->render($user, $system);
    }
    $subscriptions_html .= "</div>";
} else {
    $subscriptions_html = "<p><a href=\"login.php\">Login</a> to subscribe to channels.</p>";
}

$trends_html = "";
if (!$user->getDonotdisturb($system)) {
    $region = strtoupper(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
    $region = in_array($region, System::API_REGIONS) ? $region : "US";
    foreach (Video::fromRegion($region, $system, 5) as $video) {
        $video_preview_template->set_var("title", $video->getTitle());
        $video_preview_template->set_var("thumbnail", $video->getThumbnail());
        $video_preview_template->set_var("channel", $video->getChannel()->getName());
        $video_preview_template->set_var("channelId", $video->getChannel()->getId());
        $video_preview_template->set_var("id", $video->getId());
        $trends_html .= $video_preview_template->render($user, $system);
    }
}

$template = new Template("../templates/index.html");
$template->set_var("subscriptions", $subscriptions_html, true);
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
$page_template->set_var("title", "PrivacyTube");
$page_template->set_var("header", $header_template->render($user, $system), true);
$page_template->set_var("main", $template->render($user, $system), true);

echo $page_template->render($user, $system);
