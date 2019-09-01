<?php
require_once "Channel.php";
require_once "System.php";
require_once "User.php";
require_once "VideoSrc.php";

class Video
{
    private $id;
    private $videoSrc;
    private $title;
    private $description;
    private $channel;
    private $date;
    private $views;
    private $likes;
    private $dislikes;
    private $thumbnail;

    public static function fromId(string $id, System $system): Video
    {
        $result = $system->mysql("SELECT * FROM videos WHERE vid = ? AND date > (CURRENT_TIMESTAMP - INTERVAL 2 DAY)", "s", $id);
        if ($result->num_rows === 1) {
            $data = $result->fetch_object();
            return new Video(
                $id,
                VideoSrc::fromId($id),
                $data->title,
                $data->description,
                Channel::fromId($data->channel, $system),
                strtotime($data->vdate),
                $data->views,
                $data->likes,
                $data->dislikes,
                $data->thumbnail
            );
        }

        $data = $system->api("/videos", array("id" => $id, "part" => "statistics,snippet"), false);
        if (!isset(
            $data->items,
            $data->items[0],
            $data->items[0]->snippet,
            $data->items[0]->snippet->title,
            $data->items[0]->snippet->description,
            $data->items[0]->snippet->channelId,
            $data->items[0]->snippet->publishedAt,
            $data->items[0]->snippet->thumbnails,
            $data->items[0]->snippet->thumbnails->medium,
            $data->items[0]->snippet->thumbnails->medium->url,
            $data->items[0]->statistics,
            $data->items[0]->statistics->viewCount,
            $data->items[0]->statistics->likeCount,
            $data->items[0]->statistics->dislikeCount
        )) {
            die("Can't load Video from id ($id)");
        }

        $system->mysql(
            "INSERT INTO videos(vid, title, description, channel, vdate, views, likes, dislikes, thumbnail) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            "sssssiiis",
            $id,
            $data->items[0]->snippet->title,
            $data->items[0]->snippet->description,
            $data->items[0]->snippet->channelId,
            date("Y-m-d H:i:s", strtotime($data->items[0]->snippet->publishedAt)),
            $data->items[0]->statistics->viewCount,
            $data->items[0]->statistics->likeCount,
            $data->items[0]->statistics->dislikeCount,
            $data->items[0]->snippet->thumbnails->medium->url
        );

        return new Video(
            $id,
            VideoSrc::fromId($id),
            $data->items[0]->snippet->title,
            $data->items[0]->snippet->description,
            Channel::fromId($data->items[0]->snippet->channelId, $system),
            strtotime($data->items[0]->snippet->publishedAt),
            $data->items[0]->statistics->viewCount,
            $data->items[0]->statistics->likeCount,
            $data->items[0]->statistics->dislikeCount,
            $data->items[0]->snippet->thumbnails->medium->url
        );
    }

    public static function fromChannel(Channel $channel, System $system, int $max = 50): array
    {
        $result = array();

        $data = $system->api("/playlistItems", array("playlistId" => $channel->getUploadsId(), "part" => "snippet", "maxResults" => $max));
        if (!isset($data->items)) {
            die("Can't load Video from Channel (" . $channel->getId() . ")");
        }

        foreach ($data->items as $video) {
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
                die("Can't load Video from Channel (" . $channel->getId() . ")");
            }

            $result[] = new Video(
                $video->snippet->resourceId->videoId,
                null,
                $video->snippet->title,
                $video->snippet->description,
                $channel,
                strtotime($video->snippet->publishedAt),
                null,
                null,
                null,
                $video->snippet->thumbnails->medium->url
            );
        }

        return $result;
    }

    public static function fromUser(User $user, System $system, int $max = 50): array
    {
        $result = array();

        $subscriptions = $user->getSubscriptions($system);

        foreach ($subscriptions as $subscription) {
            $videos = self::fromChannel(Channel::fromId($subscription, $system), $system);

            foreach ($videos as $video) {
                $result[$video->getDate()] = $video;
            }
        }

        krsort($result);
        $result = array_slice($result, 0, $max);
        return $result;
    }

    public static function fromRegion(string $region, System $system, int $max = 50): array
    {
        $result = array();

        $data = $system->api("/videos", array("chart" => "mostPopular", "regionCode" => $region, "maxResults" => $max, "part" => "snippet"));
        if (!isset($data->items)) {
            die("Can't load trends of $region");
        }

        foreach ($data->items as $video) {
            if (!isset(
                $video->snippet,
                $video->snippet->title,
                $video->snippet->description,
                $video->snippet->channelId,
                $video->snippet->publishedAt,
                $video->snippet->thumbnails,
                $video->snippet->thumbnails->medium,
                $video->snippet->thumbnails->medium->url
            )) {
                die("Can't load video of trends $region");
            }

            $result[] = new self(
                $video->id,
                null,
                $video->snippet->title,
                $video->snippet->description,
                new Channel($video->snippet->channelId, $video->snippet->channelTitle, null, null, null),
                strtotime($video->snippet->publishedAt),
                null,
                null,
                null,
                $video->snippet->thumbnails->medium->url
            );
        }

        return $result;
    }

    public function __construct(string $id, ?VideoSrc $videoSrc, string $title, string $description, Channel $channel, int $date, ?int $views, ?int $likes, ?int $dislikes, string $thumbnail)
    {
        $this->id = $id;
        $this->videoSrc = $videoSrc;
        $this->title = $title;
        $this->description = $description;
        $this->channel = $channel;
        $this->date = $date;
        $this->views = $views;
        $this->likes = $likes;
        $this->dislikes = $dislikes;
        $this->thumbnail = $thumbnail;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getVideoSrc(): VideoSrc
    {
        return $this->videoSrc;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getChannel(): Channel
    {
        return $this->channel;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    public function getLikes(): int
    {
        return $this->likes;
    }

    public function getDislikes(): int
    {
        return $this->dislikes;
    }

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }
}
