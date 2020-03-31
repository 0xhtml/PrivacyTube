<?php
require_once "../classes/System.php";
require_once "../classes/Template.php";
require_once "../classes/User.php";

$system = new System();
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
        if (!User::register($_POST["username"], $_POST["password"], $system)) {
            $template->set_var("message", "Username already taken.");
        }
    }
}

$header_template = new Template("../templates/header.html");
if ($user->getLoggedin()) {
    $header_template->set_var("login", "logout.php");
    $header_template->set_var("loginl", "Logout");
} else {
    $header_template->set_var("login", "login.php");
    $header_template->set_var("loginl", "Login");
}

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", "Register - PrivacyTube");
$page_template->set_var("header", $header_template->render($user, $system), true);
$page_template->set_var("main", $template->render($user, $system), true);

echo $page_template->render($user, $system);
