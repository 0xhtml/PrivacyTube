<?php
require_once "Channel.php";
require_once "System.php";
require_once "Video.php";

class Search
{
    public static function fromQuery(string $q, System $system, int $max = 50): array
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

            $result[] = new Video(
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

    public static function fromVideo(Video $video, System $system, int $max = 50): array
    {
        $result = array();

        $videos = $system->api("/search", array("relatedToVideoId" => $video->getId(), "part" => "snippet", "type" => "video", "maxResults" => $max));
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
}
