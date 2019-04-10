<?php

class Subscriptions
{
    private $user;
    private $mySQL;
    private $API;

    public function __construct(User $user, MySQL $mySQL, API $API)
    {
        $this->user = $user;
        $this->mySQL = $mySQL;
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
                    "thumbnail" => "./dl?url=" . urlencode($video->snippet->thumbnails->default->url),
                    "channel" => $video->snippet->channelTitle,
                    "channel_id" => $video->snippet->channelId,
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
        $result = $this->mySQL->execute("SELECT * FROM subscriptions WHERE user = ?", "s", $this->user->getUser());
        $channels = array();
        while ($row = $result->fetch_object()) {
            $channels[] = $row->channel;
        }
        return $channels;
    }
}
