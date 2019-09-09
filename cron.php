<?php
require_once "classes/Channel.php";
require_once "classes/System.php";
require_once "classes/Video.php";

$system = new System("");
$result = $system->mysql("SELECT * FROM subscriptions");
$channels = array();
while ($data = $result->fetch_object()) {
    if (!in_array($data->channel, $channels)) {
        Video::fromChannel(Channel::fromId($data->channel, $system), $system, 0, false);
        $channels[] = $data->channel;
    }
}
