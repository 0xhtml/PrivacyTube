<?php
include_once "../init.php";

if (isset($_COOKIE["token"])) {
    $userAPI = new APIOAuth($_COOKIE["token"]);
} else {
    if (isset($_GET["code"])) {
        $userAPI = APIOAuth::get_from_code($client_id, $client_secret, $_GET["code"], "https://cloud.fritz.box/subscriptions");
    } else {
        APIOAuth::redirect($client_id, "https://cloud.fritz.box/subscriptions");
    }
}

$subscriptions = new APISubscriptions($userAPI, $API);
if ($subscriptions->error) {
    $subscriptions_html = "<p>Error</p>";
} else {
    $subscriptions_html = "";
    foreach ($subscriptions->get_videos() as $video) {
        $subscriptions_html .= "<p>" . $video["channel"] . " " . $video["title"] . "</p>";
    }
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
