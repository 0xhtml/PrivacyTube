<?php
class APIChannel {

    public $error = false;

    private $content;
    private $API;

    public function __construct(API $API, string $id) {
        $this->API = $API;
        $this->content = $this->API->get("/channels", array("id" => $id, "part" => "statistics,snippet,contentDetails"));
        if (count($this->content->items) == 0) {
            $this->error = true;
            return;
        }
    }

    public function get_image() {
        return $this->content->items[0]->snippet->thumbnails->high->url;
    }

    public function get_title() {
        return $this->content->items[0]->snippet->title;
    }

    public function get_subscribers() {
        return $this->content->items[0]->statistics->subscriberCount;
    }

    public function get_videos() {
        $result = array();
        $id = $this->content->items[0]->contentDetails->relatedPlaylists->uploads;
        $data = $this->API->get("/playlistItems", array("playlistId" => $id, "part" => "snippet", "maxResults" => "50"));
        foreach ($data->items as $item) {
            $result[] = new APIVideo($this->API, $item->snippet->resourceId->videoId);
        }
        return $result;
    }

}
