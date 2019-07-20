<?php

class Video
{
    private $API;
    private $mySQL;
    private $id;
    private $url;
    private $title;
    private $description;
    private $channel;
    private $date;
    private $views;
    private $likes;
    private $dislikes;
    private $thumbnail;

    public static function idToURL(string $id)
    {
        $response = file_get_contents("https://www.youtube.com/get_video_info?ps=maxres&video_id=" . urlencode($id));
        parse_str($response, $data);
        if (isset($data["url_encoded_fmt_stream_map"])) {
            parse_str($data["url_encoded_fmt_stream_map"], $urldata);
            if (isset($urldata["url"])) {
                return $urldata["url"];
            } else {
                die("Can't load video URL of $id");
            }
        } else {
            die("Can't load video URL of $id");
        }
    }

    /**
     * Video constructor
     * @param API $API
     * @param MySQL $mySQL
     * @param string $id Video id
     * @param string $url Video URL
     * @param string $title Video title
     * @param string $description Video description
     * @param Channel $channel Channel who uploaded the video
     * @param int $date Date the video was uploaded
     * @param int $views Views of the video
     * @param int $likes Likes of the video
     * @param int $dislikes Dislikes of the video
     * @param string $thumbnail Video thumbnail url
     */
    public function __construct(API $API, MySQL $mySQL, string $id, string $url, string $title, string $description, Channel $channel, int $date, int $views, int $likes, int $dislikes, string $thumbnail)
    {
        $this->API = $API;
        $this->mySQL = $mySQL;
        $this->id = $id;
        $this->url = $url;
        $this->title = $title;
        $this->description = $description;
        $this->channel = $channel;
        $this->date = $date;
        $this->views = $views;
        $this->likes = $likes;
        $this->dislikes = $dislikes;
        $this->thumbnail = $thumbnail;
    }

    /**
     * Get related videos to the current video
     * @param int $count Number of videos to get
     * @return array
     */
    public function getRelatedVideos(int $count): array
    {
        $result = array();

        $videos = $this->API->get("/search", array("relatedToVideoId" => $this->id, "part" => "snippet", "type" => "video", "maxResults" => $count));
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
    }

    /**
     * Get the video id
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the video URL
     * @return string
     */
    public function getURL(): string
    {
        return $this->url;
    }

    /**
     * Get the video title
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the video decription
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the channel who uploaded the video
     * @return Channel
     */
    public function getChannel(): Channel
    {
        return $this->channel;
    }

    /**
     * Get the date the video was uploaded
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * Get the views of the video
     * @return int
     */
    public function getViews(): int
    {
        return $this->views;
    }

    /**
     * Get the likes of the video
     * @return int
     */
    public function getLikes(): int
    {
        return $this->likes;
    }

    /**
     * Get the dislikes of the video
     * @return int
     */
    public function getDislikes(): int
    {
        return $this->dislikes;
    }

    /**
     * Get the video thumbnail url
     * @return string
     */
    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }
}
