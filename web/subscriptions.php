<?php
include_once "../init.php";

if (isset($_COOKIE["token"])) {
    $userAPI = new APIOAuth($_COOKIE["token"]);
} else {
    if (isset($_GET["code"])) {
        $userAPI = APIOAuth::get_from_code($client_id, $client_secret, $_GET["code"], "https://cloud.fritz.box/abos");
    } else {
        APIOAuth::redirect($client_id, "https://cloud.fritz.box/abos");
    }
}

$subscriptions = new APISubscriptions($userAPI);
$subscriptions->get_data();

$template = new Template("../templates/subscriptions.html");
$template->set_var("subscriptions", "No Subscriptions.");

$header_template = new Template("../templates/header.html");

$nav_template = new Template("../templates/nav.html");

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", "Abos - PrivacyTube");
$page_template->set_var("header", $header_template->render());
$page_template->set_var("nav", $nav_template->render());
$page_template->set_var("main", $template->render());

echo $page_template->render();
