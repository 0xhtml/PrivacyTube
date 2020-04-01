<?php
require_once "../../classes/APIUser.php";
require_once "../../classes/Main.php";
require_once "../../classes/Video.php";

$main = new Main();
$user = new APIUser($main);
$videos = Video::fromUser($user, $main);
$videos_array = array();
foreach ($videos as $video) {
    $videos_array[] = array(
        "id" => $video->getId(),
        "title" => $video->getTitle(),
        "description" => $video->getDescription(),
        "channel" => array(
            "id" => $video->getChannel()->getId(),
            "name" => $video->getChannel()->getName(),
            "image" => $video->getChannel()->getImage(),
            "uploadsId" => $video->getChannel()->getUploadsId()
        ),
        "date" => $video->getDate(),
        "thumbnail" => $video->getThumbnail()
    );
}
header("Content-Type: application/json");
echo json_encode($videos_array);
