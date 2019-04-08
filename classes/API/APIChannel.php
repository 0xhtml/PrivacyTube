<?php
class APIChannel {

    public $error = false;

    private $content;
    private $API;

    public function __construct(API $API, string $id) {
        $this->API = $API;
        $this->content = $this->API->get("/channels", array("id" => $id, "part" => "statistics,snippet,contentDetails"), true);
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

}
