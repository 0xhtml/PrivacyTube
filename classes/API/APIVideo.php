<?php

class APIVideo
{

    public $error = false;

    private $content;

    public function __construct(API $API, string $id)
    {
        $this->content = $API->get("/videos", array("id" => $id, "part" => "statistics,snippet"), true);
        if (count($this->content->items) == 0) {
            $this->error = true;
            return;
        }
    }

    public function get_id()
    {
        return $this->content->items[0]->id;
    }

    public function get_title()
    {
        return $this->content->items[0]->snippet->title;
    }

    public function get_description()
    {
        return $this->content->items[0]->snippet->description;
    }

    public function get_channel_id()
    {
        return $this->content->items[0]->snippet->channelId;
    }

    public function get_views()
    {
        return $this->content->items[0]->statistics->viewCount;
    }

    public function get_likes()
    {
        return $this->content->items[0]->statistics->likeCount;
    }

    public function get_dislikes()
    {
        return $this->content->items[0]->statistics->dislikeCount;
    }

    public function get_date()
    {
        return strtotime($this->content->items[0]->snippet->publishedAt);
    }

}
