<?php
require_once "../classes/Channel.php";
require_once "../classes/System.php";
require_once "../classes/Template.php";
require_once "../classes/User.php";

$system = new System();
$user = new User();

if (!isset($_GET["q"])) {
    header("Location: .");
    die();
}

if (isset($_POST["id"]) and strlen($_POST["id"]) == 24) {
    $channel = new Channel($_POST["id"], null, null, null);
    if (isset($_POST["subscribe"])) {
        $user = new User(true);
        $user->subscribe($channel, $system);
    } elseif (isset($_POST["unsubscribe"])) {
        $user = new User(true);
        $user->unsubscribe($channel, $system);
    }
}

$channel_preview_template = new Template("../templates/channelPreview.html");
$results_html = "";

foreach (Channel::fromQuery($_GET["q"], $system) as $channel) {
    $channel_preview_template->set_var("name", $channel->getName());
    $channel_preview_template->set_var("image", $channel->getImage());
    $channel_preview_template->set_var("id", $channel->getId());

    $channel_preview_template->set_var("action", "./search?q=" . $_GET["q"]);
    if ($user->getLoggedin() and $user->isSubscribed($channel, $system)) {
        $channel_preview_template->set_var("actionName", "unsubscribe");
        $channel_preview_template->set_var("actionValue", "Unsubscribe");
    } else {
        $channel_preview_template->set_var("actionName", "subscribe");
        $channel_preview_template->set_var("actionValue", "Subscribe");
    }

    $results_html .= $channel_preview_template->render($user, $system);
}

$template = new Template("../templates/search.html");
$template->set_var("search", $_GET["q"]);
$template->set_var("results", $results_html, true);

$header_template = new Template("../templates/header.html");
$header_template->set_var("search", $_GET["q"]);
if ($user->getLoggedin()) {
    $header_template->set_var("login", "logout");
    $header_template->set_var("loginl", "Logout");
} else {
    $header_template->set_var("login", "login");
    $header_template->set_var("loginl", "Login");
}

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", "Search - PrivacyTube");
$page_template->set_var("header", $header_template->render($user, $system), true);
$page_template->set_var("main", $template->render($user, $system), true);

echo $page_template->render($user, $system);
