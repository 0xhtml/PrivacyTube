<?php

class Channel
{
    private $id;
    private $name;
    private $image;
    private $subscribers;
    private $uploadsId;

    public function __construct(string $id, string $name, string $image, int $subscribers, string $uploadsId)
    {
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

    /*public function subscribe(User $user)
    {
        if ($this->is_subscribed($user)) {
            return;
        }
        $this->mySQL->execute("INSERT INTO subscriptions(user, channel) VALUES (?, ?)", "ss", $user->getUser(), $this->id);
    }

    public function is_subscribed(User $user): bool
    {
        $result = $this->mySQL->execute("SELECT * FROM subscriptions WHERE user = ? AND channel = ?", "ss", $user->getUser(), $this->id);
        if ($result->num_rows === 0) {
            return false;
        }
        return true;
    }

    public function unsubscribe(User $user)
    {
        $user = $user->getUser();
        $this->mySQL->execute("DELETE FROM subscriptions WHERE user = ? AND channel = ?", "ss", $user, $this->id);
    }

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
                "",
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
    }*/
}
