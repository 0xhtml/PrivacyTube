<?php

class APIVideo
{
    private $id;
    private $title;
    private $description;
    private $channel_id;
    private $date;
    private $views;
    private $likes;
    private $dislikes;

    public function __construct(API $API, string $id)
    {
        $this->id = $id;

        $data = $API->get("/videos", array("id" => $this->id, "part" => "statistics,snippet"));
        if (!isset(
            $data->items,
            $data->items[0],
            $data->items[0]->snippet,
            $data->items[0]->snippet->title,
            $data->items[0]->snippet->description,
            $data->items[0]->snippet->channelId,
            $data->items[0]->snippet->publishedAt,
            $data->items[0]->statistics,
            $data->items[0]->statistics->viewCount,
            $data->items[0]->statistics->likeCount,
            $data->items[0]->statistics->dislikeCount
        )) {
            die("Can't load video $this->id");
        }

        $this->title = $data->items[0]->snippet->title;
        $this->description = $data->items[0]->snippet->description;
        $this->channel_id = $data->items[0]->snippet->channelId;
        $this->date = strtotime($data->items[0]->snippet->publishedAt);
        $this->views = $data->items[0]->statistics->viewCount;
        $this->likes = $data->items[0]->statistics->likeCount;
        $this->dislikes = $data->items[0]->statistics->dislikeCount;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_title()
    {
        return $this->title;
    }

    public function get_description()
    {
        return $this->description;
    }

    public function get_channel_id()
    {
        return $this->channel_id;
    }

    public function get_date()
    {
        return $this->date;
    }

    public function get_views()
    {
        return $this->views;
    }

    public function get_likes()
    {
        return $this->likes;
    }

    public function get_dislikes()
    {
        return $this->dislikes;
    }
}
