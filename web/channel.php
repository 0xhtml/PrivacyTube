<?php
include_once "../init.php";

if (!isset($_GET["c"]) or strlen($_GET["c"]) != 24) {
    header("Location: .");
    die();
}

$channel = new APIChannel($API, $_GET["c"]);

$template = new Template("../templates/channel.html");
$template->set_var("name", $channel->get_name());
$template->set_var("subscribers", $channel->get_subscribers());
$template->set_var("image", $channel->get_image());

$header_template = new Template("../templates/header.html");

$nav_template = new Template("../templates/nav.html");

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", $channel->get_name() . " - PrivacyTube");
$page_template->set_var("header", $header_template->render());
$page_template->set_var("nav", $nav_template->render());
$page_template->set_var("main", $template->render());

echo $page_template->render();
