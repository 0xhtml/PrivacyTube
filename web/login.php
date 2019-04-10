<?php
include_once "../init.php";

if (isset($_POST["username"], $_POST["password"])) {
    User::login($mysqli, $_POST["username"], $_POST["password"]);
}

$template = new Template("../templates/login.html");
$template->set_var("subscriptions", $subscriptions_html);

$header_template = new Template("../templates/header.html");

$nav_template = new Template("../templates/nav.html");

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", "Login - PrivacyTube");
$page_template->set_var("header", $header_template->render());
$page_template->set_var("nav", $nav_template->render());
$page_template->set_var("main", $template->render());

echo $page_template->render();
