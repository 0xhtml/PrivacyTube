<?php
include_once "../init.php";

$user = new User();

$subscriptions = new UserSubscriptions($user, $mySQL, $API);

$video_preview_template = new Template("../templates/videoPreview.html");
$subscriptions_html = "";

foreach ($subscriptions->get_videos() as $video) {
    $video_preview_template->set_var("title", $video["title"]);
    $video_preview_template->set_var("thumbnail", $video["thumbnail"]);
    $video_preview_template->set_var("channel", $video["channel"]);
    $video_preview_template->set_var("channelId", $video["channel_id"]);
    $video_preview_template->set_var("id", $video["id"]);
    $subscriptions_html .= $video_preview_template->render();
}

$template = new Template("../templates/index.html");
$template->set_var("subscriptions", $subscriptions_html);

$header_template = new Template("../templates/header.html");

$nav_template = new Template("../templates/nav.html");

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", "Subscriptions - PrivacyTube");
$page_template->set_var("header", $header_template->render());
$page_template->set_var("nav", $nav_template->render());
$page_template->set_var("main", $template->render());

echo $page_template->render();
