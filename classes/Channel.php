<?php

class Channel
{
    private $API;
    private $mySQL;

    private $id;
    private $name;
    private $image;
    private $subscribers;
    private $uploadsId;

    public function __construct(API $API, MySQL $mySQL, string $id, string $name, string $image, int $subscribers, string $uploadsId)
    {
        $this->API = $API;
        $this->mySQL = $mySQL;

        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
        $this->subscribers = $subscribers;
        $this->uploadsId = $uploadsId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getSubscribers(): int
    {
        return $this->subscribers;
    }

    public function getPrivateSubscribers(): int
    {
        return $this->mySQL->execute("SELECT * FROM subscriptions WHERE channel = ?", "s", $this->id)->num_rows;
    }

    public function subscribe(User $user, string $channel)
    {
        if ($this->is_subscribed($user)) {
            return;
        }

        $user = $user->getUser();
        $this->mySQL->execute("INSERT INTO subscriptions(user, channel) VALUES (?, ?)", "ss", $user, $channel);
    }

    public function is_subscribed(User $user)
    {
        $user = $user->getUser();
        $result = $this->mySQL->execute("SELECT * FROM subscriptions WHERE user = ? AND channel = ?", "ss", $user, $this->id);
        if ($result->num_rows === 0) {
            return false;
        } else {
            return true;
        }
    }

    public function unsubscribe(User $user, string $channel)
    {
        $user = $user->getUser();
        $this->mySQL->execute("DELETE FROM subscriptions WHERE user = ? AND channel = ?", "ss", $user, $channel);
    }

    public function getVideos()
    {
        $result = array();

        $videos = $this->API->get("/playlistItems", array("playlistId" => $this->uploadsId, "part" => "snippet", "maxResults" => 50));
        if (!isset($videos->items)) {
            die("Can't load videos of channel");
        }

        foreach ($videos->items as $video) {
            if (!isset($video->snippet, $video->snippet->publishedAt, $video->snippet->title, $video->snippet->thumbnails, $video->snippet->thumbnails->default, $video->snippet->thumbnails->default->url, $video->snippet->channelTitle, $video->snippet->resourceId, $video->snippet->resourceId->videoId)) {
                die("Can't load video of channel");
            }

            $result[strtotime($video->snippet->publishedAt)] = array(
                "title" => $video->snippet->title,
                "thumbnail" => "./dl.php?url=" . urlencode($video->snippet->thumbnails->default->url),
                "channel" => $video->snippet->channelTitle,
                "channel_id" => $video->snippet->channelId,
                "id" => $video->snippet->resourceId->videoId
            );
        }

        ksort($result);
        $result = array_reverse($result);
        $result = array_slice($result, 0, 50);
        return $result;
    }
}
