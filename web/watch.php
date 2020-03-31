<?php
require_once "../classes/Main.php";
require_once "../classes/Template.php";
require_once "../classes/User.php";
require_once "../classes/Video.php";

if (!isset($_GET["v"]) or strlen($_GET["v"]) != 11) {
    header("Location: .");
    die();
}

$main = new Main();
$user = new User();

$video = Video::fromId($_GET["v"], $main);

if ($user->getLoggedin()) {
    $main->mysql("UPDATE ai SET eval = 0 WHERE user = ? AND id = ?", "is", $user->getUser(), $video->getId());
    if ($user->isSubscribed($video->getChannel(), $main)) {
        $subscriptions = $user->getSubscriptions($main);
        $ai = Video::fromVideo($video, $main);
        foreach ($ai as $aiVideo) {
            if (in_array($aiVideo->getChannel()->getId(), $subscriptions)) {
                continue;
            }
            $result = $main->mysql("SELECT * FROM ai WHERE user = ? AND id = ?", "is", $user->getUser(), $aiVideo->getId());
            if ($result->num_rows === 1) {
                $main->mysql("UPDATE ai SET eval = eval + 1 WHERE user = ? AND id = ? AND eval > 0", "is", $user->getUser(), $aiVideo->getId());
            } else {
                $main->mysql(
                    "INSERT INTO ai(user, id, title, channel, channelname, thumbnail, eval) VALUES (?, ?, ?, ?, ?, ?, 1)",
                    "isssss",
                    $user->getUser(),
                    $aiVideo->getId(),
                    $aiVideo->getTitle(),
                    $aiVideo->getChannel()->getId(),
                    $aiVideo->getChannel()->getName(),
                    $aiVideo->getThumbnail()
                );
            }
        }
    }
}

$template = new Template("../templates/watch.html");
$template->set_var("videoId", $video->getId());
$template->set_var("videoSrc", $video->getVideoSrc()->getHtml(), true);
$template->set_var("videoTitle", $video->getTitle());
$template->set_var("videoDescription", nl2br(htmlspecialchars($video->getDescription())), true);
$template->set_var("videoDate", date("d. M Y H:s", $video->getDate()));

$template->set_var("channelId", $video->getChannel()->getId());
$template->set_var("channelName", $video->getChannel()->getName());
$template->set_var("channelImage", $video->getChannel()->getImage());

$header_template = new Template("../templates/header.html");
$header_template->set_var("login", !$user->getLoggedin() ? "in" : "out");

$page_template = new Template("../templates/page.html");
$page_template->set_var("title", $video->getTitle() . " - PrivacyTube");
$page_template->set_var("header", $header_template->render($user, $main), true);
$page_template->set_var("main", $template->render($user, $main), true);

echo $page_template->render($user, $main);
