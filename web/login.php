<?php
require_once "../classes/Main.php";
require_once "../classes/Template.php";
require_once "../classes/User.php";

$main = new Main();
$user = new User();

$template = new Template("../templates/login.html");

if (isset($_POST["username"], $_POST["password"])) {
    if (!User::login($_POST["username"], $_POST["password"], $main)) {
        $template->set_var("message", "Invalid username or password");
        $template->set_var("username", $_POST["username"]);
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
$page_template->set_var("title", "Login - PrivacyTube");
$page_template->set_var("header", $header_template->render($user, $main), true);
$page_template->set_var("main", $template->render($user, $main), true);

echo $page_template->render($user, $main);
