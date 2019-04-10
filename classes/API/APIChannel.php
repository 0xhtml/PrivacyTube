<?php

class APIChannel
{
    private $image;
    private $title;
    private $subscribers;

    public function __construct(API $API, string $id)
    {
        $data = $API->get("/channels", array("id" => $id, "part" => "statistics,snippet"));
        if (!isset(
            $data->items,
            $data->items[0],
            $data->items[0]->snippet,
            $data->items[0]->snippet->title,
            $data->items[0]->snippet->thumbnails,
            $data->items[0]->snippet->thumbnails->default,
            $data->items[0]->snippet->thumbnails->default->url,
            $data->items[0]->statistics,
            $data->items[0]->statistics->subscriberCount
        )) {
            die("Can't load channel $id");
        }

        $this->title = $data->items[0]->snippet->title;
        $this->image = $data->items[0]->snippet->thumbnails->default->url;
        $this->subscribers = $data->items[0]->statistics->subscriberCount;
    }

    public function get_title()
    {
        return $this->title;
    }

    public function get_image()
    {
        return $this->image;
    }

    public function get_subscribers()
    {
        return $this->subscribers;
    }
}
