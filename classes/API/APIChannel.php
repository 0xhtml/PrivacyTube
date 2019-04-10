<?php

class APIChannel
{
    private $id;
    private $name;
    private $image;
    private $subscribers;

    public function __construct(API $API, string $id)
    {
        $this->id = $id;

        $data = $API->get("/channels", array("id" => $this->id, "part" => "statistics,snippet"));
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
            die("Can't load channel $this->id");
        }

        $this->name = $data->items[0]->snippet->title;
        $this->image = "./dl?url=" . urlencode($data->items[0]->snippet->thumbnails->default->url);
        $this->subscribers = $data->items[0]->statistics->subscriberCount;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_name()
    {
        return $this->name;
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
