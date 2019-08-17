<?php
require_once "../classes/Config.php";
require_once "../classes/MySQL.php";
require_once "../classes/Template.php";
require_once "../classes/User.php";

$template = new Template("../templates/login.html");

if (isset($_POST["username"], $_POST["password"])) {
    $config = new Config();
    $mySQL = new MySQL($config);
    if (!User::login($mySQL, $_POST["username"], $_POST["password"])) {
        $template->set_var("message", "Invalid username or password");
        $template->set_var("username", $_POST["username"]);
    }
}

$header_template = new Template("../templates/header.html");

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", "Login - PrivacyTube");
$page_template->set_var("header", $header_template->render());
$page_template->set_var("main", $template->render());

echo $page_template->render();
