<?php
include_once "../init.php";

if (!isset($_GET["c"]) or strlen($_GET["c"]) != 24) {
    header("Location: .");
    die();
}

$channel = new APIChannel($API, $_GET["c"]);

if (isset($_POST["subscribe"])) {
    $user = new User();
    $user_subscriptions = new UserSubscriptions($user, $mysqli, $API);
    $user_subscriptions->subscribe($channel->get_id());
} elseif (isset($_POST["unsubscribe"])) {
    $user = new User();
    $user_subscriptions = new UserSubscriptions($user, $mysqli, $API);
    $user_subscriptions->unsubscribe($channel->get_id());
} elseif (session_status() == PHP_SESSION_NONE and isset($_COOKIE["PHPSESSID"])) {
    session_start();
    if (isset($_SESSION["user"])) {
        $user = new User();
        $user_subscriptions = new UserSubscriptions($user, $mysqli, $API);
    }
}

$template = new Template("../templates/channel.html");
$template->set_var("id", $channel->get_id());
$template->set_var("name", $channel->get_name());
$template->set_var("subscribers", number_format($channel->get_subscribers()));
$template->set_var("image", $channel->get_image());
if (isset($user)) {
    if ($user_subscriptions->is_subscribed_to($channel->get_id())) {
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
$page_template->set_var("title", $channel->get_name() . " - PrivacyTube");
$page_template->set_var("header", $header_template->render());
$page_template->set_var("nav", $nav_template->render());
$page_template->set_var("main", $template->render());

echo $page_template->render();
