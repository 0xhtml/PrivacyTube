<?php

class Subscriptions
{
    private $user;
    private $mySQL;
    private $API;

    /**
     * Subscriptions constructor
     * @param User $user
     * @param MySQL $mySQL
     * @param API $API
     */
    public function __construct(User $user, MySQL $mySQL, API $API)
    {
        $this->user = $user;
        $this->mySQL = $mySQL;
        $this->API = $API;
    }

    /**
     * Get the videos the users subscribed channels uploaded ordered from new to old
     * @param int $count Number of videos to get
     * @return array
     */
    public function getVideos(int $count): array
    {
        $result = array();

        $channels = $this->getChannels();
        $channels = join(",", $channels);

        $data = $this->API->get("/channels", array("id" => $channels, "part" => "contentDetails"));
        if (!isset($data->items)) {
            die("Can't load subscribed channels of user");
        }

        foreach ($data->items as $channel) {
            if (!isset(
                $channel->id,
                $channel->contentDetails,
                $channel->contentDetails->relatedPlaylists,
                $channel->contentDetails->relatedPlaylists->uploads
            )) {
                die("Can't load subscribed channels uploads");
            }

            $videos = $this->API->get("/playlistItems", array("playlistId" => $channel->contentDetails->relatedPlaylists->uploads, "part" => "snippet", "maxResults" => 50));
            if (!isset($videos->items)) {
                die("Can't load videos of subscribed channel $channel->id");
            }

            foreach ($videos->items as $video) {
                if (!isset(
                    $video->snippet,
                    $video->snippet->publishedAt,
                    $video->snippet->title,
                    $video->snippet->description,
                    $video->snippet->channelId,
                    $video->snippet->channelTitle,
                    $video->snippet->thumbnails,
                    $video->snippet->thumbnails->medium,
                    $video->snippet->thumbnails->medium->url,
                    $video->snippet->resourceId,
                    $video->snippet->resourceId->videoId
                )) {
                    die("Can't load video of subscribed channel $channel->id");
                }

                $result[strtotime($video->snippet->publishedAt)] = new Video(
                    $this->API,
                    $this->mySQL,
                    $video->snippet->resourceId->videoId,
                    "",
                    $video->snippet->title,
                    $video->snippet->description,
                    new Channel($this->API, $this->mySQL, $video->snippet->channelId, $video->snippet->channelTitle, "", 0, ""),
                    strtotime($video->snippet->publishedAt),
                    0,
                    0,
                    0,
                    $video->snippet->thumbnails->medium->url
                );
            }
        }

        ksort($result);
        $result = array_reverse($result);
        $result = array_slice($result, 0, $count);
        return $result;
    }

    /**
     * Get the channels the user is subscribed to
     * @return array
     */
    public function getChannels()
    {
        $result = $this->mySQL->execute("SELECT * FROM subscriptions WHERE user = ?", "s", $this->user->getUser());
        $channels = array();
        while ($row = $result->fetch_object()) {
            $channels[] = $row->channel;
        }
        return $channels;
    }
}
