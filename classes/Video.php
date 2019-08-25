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
        $data = $system->api("/videos", array("id" => $id, "part" => "statistics,snippet"));
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
        $subscriptions = join(",", $subscriptions);

        $data = $system->api("/channels", array("id" => $subscriptions, "part" => "contentDetails"));
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

            $videos = $system->api("/playlistItems", array("playlistId" => $channel->contentDetails->relatedPlaylists->uploads, "part" => "snippet", "maxResults" => 50));
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
                    $video->snippet->resourceId->videoId,
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

    public static function fromSearch(string $q, System $system, int $max = 50)
    {
        $result = array();

        $videos = $system->api("/search", array("q" => $q, "part" => "snippet", "type" => "video", "maxResults" => $max), false);
        if (!isset($videos->items)) {
            die("Can't load searched videos");
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
                $video->id,
                $video->id->videoId
            )) {
                die("Can't load searched video");
            }

            $result[] = new self(
                $video->id->videoId,
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

    /*public function getRelatedVideos(int $count): array
    {
        $result = array();

        $videos = $this->system->api("/search", array("relatedToVideoId" => $this->id, "part" => "snippet", "type" => "video", "maxResults" => $count));
        if (!isset($videos->items)) {
            die("Can't load related videos");
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
                $video->id,
                $video->id->videoId
            )) {
                die("Can't load related video");
            }

            $result[] = new Video(
                $this->API,
                $this->mySQL,
                $video->id->videoId,
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

        return $result;
    }*/

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
