<?php
include_once "../init.php";

if (isset($_COOKIE["token"])) {
    $userAPI = new APIOAuth($_COOKIE["token"]);
} else {
    if (isset($_GET["code"])) {
        APIOAuth::redirect_code($client_id, $client_secret, $_GET["code"], "https://cloud.fritz.box/subscriptions");
    } else {
        APIOAuth::redirect($client_id, "https://cloud.fritz.box/subscriptions");
    }
}

$subscriptions = new APISubscriptions($userAPI, $API);

$video_preview_template = new Template("../templates/videoPreview.html");
$subscriptions_html = "";

foreach ($subscriptions->get_videos() as $video) {
    $video_preview_template->set_var("title", $video["title"]);
    $video_preview_template->set_var("thumbnail", $video["thumbnail"]);
    $video_preview_template->set_var("channel", $video["channel"]);
    $video_preview_template->set_var("id", $video["id"]);
    $subscriptions_html .= $video_preview_template->render();
}

$template = new Template("../templates/subscriptions.html");
$template->set_var("subscriptions", $subscriptions_html);

$header_template = new Template("../templates/header.html");

$nav_template = new Template("../templates/nav.html");

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", "Subscriptions - PrivacyTube");
$page_template->set_var("header", $header_template->render());
$page_template->set_var("nav", $nav_template->render());
$page_template->set_var("main", $template->render());

echo $page_template->render();
