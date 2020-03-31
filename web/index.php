<?php
require_once "../classes/Main.php";
require_once "../classes/Template.php";
require_once "../classes/User.php";
require_once "../classes/Video.php";

$main = new Main();
$user = new User(true);

$video_preview_template = new Template("../templates/videoPreview.html");

$ai_html = "";
foreach (Video::fromAI($user, $main, 5) as $video) {
    $video_preview_template->set_var("title", $video->getTitle());
    $video_preview_template->set_var("thumbnail", $video->getThumbnail());
    $video_preview_template->set_var("channel", $video->getChannel()->getName());
    $video_preview_template->set_var("id", $video->getId());
    $ai_html .= $video_preview_template->render($user, $main);
}

$subscriptions_html = "";
foreach (Video::fromUser($user, $main) as $video) {
    $video_preview_template->set_var("title", $video->getTitle());
    $video_preview_template->set_var("thumbnail", $video->getThumbnail());
    $video_preview_template->set_var("channel", $video->getChannel()->getName());
    $video_preview_template->set_var("id", $video->getId());
    $subscriptions_html .= $video_preview_template->render($user, $main);
}

$template = new Template("../templates/index.html");
$template->set_var("ai", $ai_html, true);
$template->set_var("subscriptions", $subscriptions_html, true);

$header_template = new Template("../templates/header.html");
$header_template->set_var("login", $user->getLoggedin() ? "in" : "out");

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", "PrivacyTube");
$page_template->set_var("header", $header_template->render($user, $main), true);
$page_template->set_var("main", $template->render($user, $main), true);

echo $page_template->render($user, $main);
