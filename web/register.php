<?php
require_once "../classes/Main.php";
require_once "../classes/Template.php";
require_once "../classes/User.php";

$main = new Main();
$user = new User();

$template = new Template("../templates/register.html");

if (isset($_POST["username"], $_POST["password"], $_POST["password2"])) {
    if (empty($_POST["username"]) or empty($_POST["password"]) or empty($_POST["password2"])) {
        $template->set_var("message", "Please fill out all fields.");
        $template->set_var("username", $_POST["username"]);
    } elseif ($_POST["password"] !== $_POST["password2"]) {
        $template->set_var("message", "Password conformation invalid.");
        $template->set_var("username", $_POST["username"]);
    } else {
        if (!User::register($_POST["username"], $_POST["password"], $main)) {
            $template->set_var("message", "Username already taken.");
        }
    }
}

$header_template = new Template("../templates/header.html");
$header_template->set_var("login", $user->getLoggedin() ? "in" : "out");

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", "Register - PrivacyTube");
$page_template->set_var("header", $header_template->render($user, $main), true);
$page_template->set_var("main", $template->render($user, $main), true);

echo $page_template->render($user, $main);
