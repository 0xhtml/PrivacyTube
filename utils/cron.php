<?php
require_once "classes/Channel.php";
require_once "classes/Main.php";
require_once "classes/Video.php";

$main = new Main();
$result = $main->mysql("SELECT * FROM subscriptions");
$channels = array();
while ($data = $result->fetch_object()) {
    if (!in_array($data->channel, $channels)) {
        Video::fromChannel(Channel::fromId($data->channel, $main), $main, 0, false);
        $channels[] = $data->channel;
    }
}
