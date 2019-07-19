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

    /**
     * Channel constructor
     * @param API $API
     * @param MySQL $mySQL
     * @param string $id Channel id
     * @param string $name Channel name
     * @param string $image Channel image url
     * @param int $subscribers Number of subscribers
     * @param string $uploadsId Playlist id of the channels uploads
     */
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

    /**
     * Get the channel id
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the channel name
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the channel image url
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * Get the number of YouTube subscribers to the channel
     * @return int
     */
    public function getSubscribers(): int
    {
        return $this->subscribers;
    }

    /**
     * Get the number of PrivacyTube subscribers to the channel
     * @return int
     */
    public function getPrivateSubscribers(): int
    {
        return $this->mySQL->execute("SELECT * FROM subscriptions WHERE channel = ?", "s", $this->id)->num_rows;
    }

    /**
     * Subscribe a user to this channel
     * @param User $user
     */
    public function subscribe(User $user)
    {
        if ($this->is_subscribed($user)) {
            return;
        }
        $this->mySQL->execute("INSERT INTO subscriptions(user, channel) VALUES (?, ?)", "ss", $user->getUser(), $this->id);
    }

    /**
     * Check if a user is subscribed to this channel
     * @param User $user
     * @return bool
     */
    public function is_subscribed(User $user): bool
    {
        $result = $this->mySQL->execute("SELECT * FROM subscriptions WHERE user = ? AND channel = ?", "ss", $user->getUser(), $this->id);
        if ($result->num_rows === 0) {
            return false;
        }
        return true;
    }

    /**
     * Unsubscribe a user from this channel
     * @param User $user
     * @param string $channel
     */
    public function unsubscribe(User $user)
    {
        $user = $user->getUser();
        $this->mySQL->execute("DELETE FROM subscriptions WHERE user = ? AND channel = ?", "ss", $user, $this->id);
    }

    /**
     * Get the videos the channel uploaded ordered from new to old
     * @param int $count The number of videos to get
     * @return array
     */
    public function getVideos(int $count): array
    {
        $result = array();

        $videos = $this->API->get("/playlistItems", array("playlistId" => $this->uploadsId, "part" => "snippet", "maxResults" => $count));
        if (!isset($videos->items)) {
            die("Can't load videos of channel");
        }

        foreach ($videos->items as $video) {
            if (!isset(
                $video->snippet,
                $video->snippet->publishedAt,
                $video->snippet->title,
                $video->snippet->description,
                $video->snippet->thumbnails,
                $video->snippet->thumbnails->medium,
                $video->snippet->thumbnails->medium->url,
                $video->snippet->resourceId,
                $video->snippet->resourceId->videoId
            )) {
                die("Can't load video of channel");
            }

            $result[] = new Video(
                $this->API,
                $this->mySQL,
                $video->snippet->resourceId->videoId,
                $video->snippet->title,
                $video->snippet->description,
                $this,
                strtotime($video->snippet->publishedAt),
                0,
                0,
                0,
                $video->snippet->thumbnails->medium->url
            );
        }

        return $result;
    }
}
