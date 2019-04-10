<?php

class UserSubscriptions
{

    private $user;
    private $mysqli;
    private $API;

    public function __construct(User $user, mysqli $mysqli, API $API)
    {
        $this->user = $user;
        $this->mysqli = $mysqli;
        $this->API = $API;
    }

    public function get_videos()
    {
        $result = array();

        $channels = $this->get_channels();
        $channels = join(",", $channels);

        $data = $this->API->get("/channels", array("id" => $channels, "part" => "contentDetails", "maxResults" => 50));
        if (!isset($data->items)) {
            die("Can't load subscribed channels of user");
        }

        foreach ($data->items as $channel) {
            if (!isset($channel->id)) {
                die("Can't load subscribed channel");
            }
            if (!isset($channel->contentDetails, $channel->contentDetails->relatedPlaylists, $channel->contentDetails->relatedPlaylists->uploads)) {
                die("Can't load upload playlist id of subscribed channel $channel->id");
            }

            $videos = $this->API->get("/playlistItems", array("playlistId" => $channel->contentDetails->relatedPlaylists->uploads, "part" => "snippet", "maxResults" => 50));
            if (!isset($videos->items)) {
                die("Can't load videos of subscribed channel $channel->id");
            }

            foreach ($videos->items as $video) {
                if (!isset($video->snippet, $video->snippet->publishedAt, $video->snippet->title, $video->snippet->thumbnails, $video->snippet->thumbnails->default, $video->snippet->thumbnails->default->url, $video->snippet->channelTitle, $video->snippet->resourceId, $video->snippet->resourceId->videoId)) {
                    die("Can't load video of subscribed channel $channel->id");
                }

                $result[strtotime($video->snippet->publishedAt)] = array(
                    "title" => $video->snippet->title,
                    "thumbnail" => $video->snippet->thumbnails->default->url,
                    "channel" => $video->snippet->channelTitle,
                    "id" => $video->snippet->resourceId->videoId
                );
            }
        }

        ksort($result);
        $result = array_reverse($result);
        $result = array_slice($result, 0, 50);
        return $result;
    }

    public function get_channels()
    {
        $statement = $this->mysqli->prepare("SELECT * FROM subscriptions WHERE user = ?");
        $user = $this->user->get_user();
        $statement->bind_param("s", $user);
        if (!$statement->execute()) {
            die("Can't load subscribed channels: $statement->error");
        }
        $result = $statement->get_result();
        $channels = array();
        while ($row = $result->fetch_object()) {
            $channels[] = $row->channel;
        }
        return $channels;
    }

    public function subscribe(string $channel)
    {
        $statement = $this->mysqli->prepare("INSERT INTO subscriptions(user, channel) VALUES (?, ?)");
        $user = $this->user->get_user();
        $statement->bind_param("ss", $user, $channel);
        if (!$statement->execute()) {
            die("Can't subscribe to channel: $statement->error");
        }
    }

}
