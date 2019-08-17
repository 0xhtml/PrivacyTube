<?php

class Search
{
    private $API;
    private $mySQL;
    private $q;

    public function __construct(API $API, MySQL $mySQL, string $q)
    {
        $this->API = $API;
        $this->mySQL = $mySQL;
        $this->q = $q;
    }

    public function getResults(int $count)
    {
        $result = array();

        $videos = $this->API->get("/search", array("q" => $this->q, "part" => "snippet", "type" => "video", "maxResults" => $count), false);
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
}
