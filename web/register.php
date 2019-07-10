<?php
require_once "../classes/Config.php";
require_once "../classes/MySQL.php";
require_once "../classes/User.php";
require_once "../classes/Template.php";

User::checkLogin();

$template = new Template("../templates/register.html");

if (isset($_POST["username"], $_POST["password"], $_POST["password2"])) {
    if (empty($_POST["username"]) or empty($_POST["password"]) or empty($_POST["password2"])) {
        $template->set_var("message", "Please fill out all fields.");
        $template->set_var("username", $_POST["username"]);
    } elseif ($_POST["password"] !== $_POST["password2"]) {
        $template->set_var("message", "Password conformation invalid.");
        $template->set_var("username", $_POST["username"]);
    } else {
        $config = new Config();
        $mySQL = new MySQL($config);
        if ((!User::register($mySQL, $_POST["username"], $_POST["password"]))) {
            $template->set_var("message", "Username already taken.");
        }
    }
}

$header_template = new Template("../templates/header.html");

$nav_template = new Template("../templates/nav.html");

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", "Register - PrivacyTube");
$page_template->set_var("header", $header_template->render());
$page_template->set_var("nav", $nav_template->render());
$page_template->set_var("main", $template->render());

echo $page_template->render();
