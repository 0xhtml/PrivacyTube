<?php
include_once "../init.php";

if (!isset($_GET["c"]) or strlen($_GET["c"]) != 24) {
    header("Location: .");
    die();
}

$channel = $API->getChannel($_GET["c"]);

if (isset($_POST["subscribe"])) {
    $user = new User();
    $channel->subscribe($user, $channel->getId());
} elseif (isset($_POST["unsubscribe"])) {
    $user = new User();
    $channel->unsubscribe($user, $channel->getId());
} elseif (session_status() == PHP_SESSION_NONE and isset($_COOKIE["PHPSESSID"])) {
    session_start();
    if (isset($_SESSION["user"])) {
        $user = new User();
    }
}

$template = new Template("../templates/channel.html");
$template->set_var("id", $channel->getId());
$template->set_var("name", $channel->getName());
$template->set_var("subscribers", number_format($channel->getSubscribers()));
$template->set_var("image", $channel->getImage());
if (isset($user)) {
    if ($channel->is_subscribed($user)) {
        $template->set_var("action", "unsubscribe");
        $template->set_var("actionValue", "Unsubscribe");
    } else {
        $template->set_var("action", "subscribe");
        $template->set_var("actionValue", "Subscribe");
    }
} else {
    $template->set_var("action", "subscribe");
    $template->set_var("actionValue", "Subscribe");
}


$header_template = new Template("../templates/header.html");

$nav_template = new Template("../templates/nav.html");

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", $channel->getName() . " - PrivacyTube");
$page_template->set_var("header", $header_template->render());
$page_template->set_var("nav", $nav_template->render());
$page_template->set_var("main", $template->render());

echo $page_template->render();
