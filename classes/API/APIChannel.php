<?php
class APIChannel {

    public $error = false;

    private $content;

    public function __construct(API $API, string $id) {
        $this->content = $API->get("/channels", array("id" => $id, "part" => "statistics,snippet"));
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
