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
    private $thumbnail;

    public static function fromId(string $id, System $system): Video
    {
        $result = $system->mysql("SELECT * FROM videos WHERE id = ?", "s", $id);
        if ($result->num_rows === 1) {
            $data = $result->fetch_object();
            return new Video(
                $id,
                VideoSrc::fromId($id),
                $data->title,
                $data->description,
                Channel::fromId($data->channel, $system),
                strtotime($data->date),
                $data->thumbnail
            );
        }

        $data = $system->api("/videos", array("id" => $id, "part" => "snippet"), false);
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
            $data->items[0]->snippet->thumbnails->medium->url
        )) {
            die("Can't load Video from id ($id)");
        }

        $system->mysql(
            "INSERT INTO videos(sql_state, id, title, description, channel, date, thumbnail) VALUES (0, ?, ?, ?, ?, ?, ?)",
            "ssssss",
            $id,
            $data->items[0]->snippet->title,
            $data->items[0]->snippet->description,
            $data->items[0]->snippet->channelId,
            date("Y-m-d H:i:s", strtotime($data->items[0]->snippet->publishedAt)),
            $data->items[0]->snippet->thumbnails->medium->url
        );

        return new Video(
            $id,
            VideoSrc::fromId($id),
            $data->items[0]->snippet->title,
            $data->items[0]->snippet->description,
            Channel::fromId($data->items[0]->snippet->channelId, $system),
            strtotime($data->items[0]->snippet->publishedAt),
            $data->items[0]->snippet->thumbnails->medium->url
        );
    }

    public static function fromChannel(Channel $channel, System $system, int $max = 50): array
    {
        $videos = array();
        
        $result = $system->mysql("SELECT * FROM videos WHERE channel = ? AND sql_state = 1 ORDER BY date LIMIT ?", "si", $channel->getId(), $max);
        if ($result->num_rows !== 0) {
            while($data = $result->fetch_object()) {
                $videos[] = new Video(
                    $data->id,
                    null,
                    $data->title,
                    $data->description,
                    $channel,
                    strtotime($data->date),
                    $data->thumbnail
                );
            }
            return $videos;
        }

        $data = $system->api("/playlistItems", array("playlistId" => $channel->getUploadsId(), "part" => "snippet", "maxResults" => 50));
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

            $system->mysql(
                "INSERT INTO videos(sql_state, id, title, description, channel, date, thumbnail) VALUES (1, ?, ?, ?, ?, ?, ?)",
                "ssssss",
                $video->snippet->resourceId->videoId,
                $video->snippet->title,
                $video->snippet->description,
                $channel->getId(),
                date("Y-m-d H:i:s", strtotime($video->snippet->publishedAt)),
                $video->snippet->thumbnails->medium->url
            );

            $videos[] = new Video(
                $video->snippet->resourceId->videoId,
                null,
                $video->snippet->title,
                $video->snippet->description,
                $channel,
                strtotime($video->snippet->publishedAt),
                $video->snippet->thumbnails->medium->url
            );
        }

        $videos = array_slice($videos, 0, $max);
        return $videos;
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
                new Channel($video->snippet->channelId, $video->snippet->channelTitle, null, null),
                strtotime($video->snippet->publishedAt),
                $video->snippet->thumbnails->medium->url
            );
        }

        return $result;
    }

    public function __construct(string $id, ?VideoSrc $videoSrc, string $title, string $description, Channel $channel, int $date, string $thumbnail)
    {
        $this->id = $id;
        $this->videoSrc = $videoSrc;
        $this->title = $title;
        $this->description = $description;
        $this->channel = $channel;
        $this->date = $date;
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

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }
}
