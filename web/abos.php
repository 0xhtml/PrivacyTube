<?php
include_once "../init.php";

if (isset($_COOKIE["token"])) {
    $userAPI = new API($_COOKIE["token"]);
} else {
    if (isset($_GET["code"])) {
        $userAPI = new APIOAuth($client_id, $client_secret, $_GET["code"], "https://cloud.fritz.box/abos");
    } else {
        APIOAuth::redirect($client_id, "https://cloud.fritz.box/abos");
    }
}

$header_template = new Template("../templates/header.html");

$nav_template = new Template("../templates/nav.html");

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", "Abos - PrivacyTube");
$page_template->set_var("header", $header_template->render());
$page_template->set_var("nav", $nav_template->render());

echo $page_template->render();
