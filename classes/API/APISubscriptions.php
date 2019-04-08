<?php
class APISubscriptions {

    public $error = false;

    private $content;
    private $API;

    public function __construct(APIOAuth $privateAPI, API $API) {
        $this->API = $API;
        $this->content = $privateAPI->get("/subscriptions", array("mine" => "true", "part" => "snippet", "maxResults" => "50"));
        if (count($this->content->items) == 0) {
            $this->error = true;
            return;
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
        foreach ($data->items as $item) {
            $videos = $this->API->get("/playlistItems", array("playlistId" => $item->contentDetails->relatedPlaylists->uploads, "part" => "snippet", "maxResults" => "1"));
            $result[] = array(
                "title" => $videos->items[0]->snippet->title,
                "thumbnail" => $videos->items[0]->snippet->thumbnails->maxres->url,
                "channel" => $videos->items[0]->snippet->channelTile
            );
        }
        return $result;
    }

}
