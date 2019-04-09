<?php
class APISubscriptions {

    private $content;
    private $API;

    public function __construct(APIOAuth $privateAPI, API $API) {
        $this->API = $API;
        $this->content = $privateAPI->get("/subscriptions", array("mine" => "true", "part" => "snippet", "maxResults" => 50));
        if (!isset($this->content->items) or count($this->content->items) == 0) {
            debug("Could not load subscriptions");
            die();
        }
    }

    public function get_videos() {
        $result = array();
        $channels = array();
        foreach ($this->content->items as $subscription) {
            $channels[] = $subscription->snippet->resourceId->channelId;
        }
        $channels = join(",", $channels);
        $data = $this->API->get("/channels", array("id" => $channels, "part" => "contentDetails", "maxResults" => 50));
        if (!isset($data->items)) {
            debug("Could not load subscribed channels");
            return array();
        }
        foreach ($data->items as $channel) {
            $videos = $this->API->get("/playlistItems", array("playlistId" => $channel->contentDetails->relatedPlaylists->uploads, "part" => "snippet", "maxResults" => 50));
            if (isset($videos->items)) {
                $i = 0;
                foreach ($videos->items as $video) {
                    if (isset($video->snippet, $video->snippet->publishedAt, $video->snippet->title, $video->snippet->thumbnails, $video->snippet->thumbnails->maxres, $video->snippet->thumbnails->maxres->url, $video->snippet->channelTitle)) {
                        $result[strtotime($video->snippet->publishedAt)] = array(
                            "title" => $video->snippet->title,
                            "thumbnail" => $video->snippet->thumbnails->maxres->url,
                            "channel" => $video->snippet->channelTitle
                        );
                    } else {
                        debug("Could not load video $i of $channel->id");
                    }
                    $i++;
                }
            } else {
                debug("Could not load videos of $channel->id");
            }
        }
        ksort($result);
        return array_reverse($result);
    }

}
