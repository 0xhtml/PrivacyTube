<?php
require_once "API.php";

class Channel
{
    private $id;
    private $name;
    private $image;
    private $subscribers;
    private $uploadsId;

    public static function fromId(string $id, API $API)
    {
        $data = $API->get("/channels", array("id" => $id, "part" => "statistics,snippet,contentDetails"));
        if (!isset(
            $data->items,
            $data->items[0],
            $data->items[0]->snippet,
            $data->items[0]->snippet->title,
            $data->items[0]->snippet->thumbnails,
            $data->items[0]->snippet->thumbnails->default,
            $data->items[0]->snippet->thumbnails->default->url,
            $data->items[0]->statistics,
            $data->items[0]->statistics->subscriberCount,
            $data->items[0]->contentDetails,
            $data->items[0]->contentDetails->relatedPlaylists,
            $data->items[0]->contentDetails->relatedPlaylists->uploads
        )) {
            die("Can't load Channel from id ($id)");
        }

        return new Channel(
            $id,
            $data->items[0]->snippet->title,
            $data->items[0]->snippet->thumbnails->default->url,
            $data->items[0]->statistics->subscriberCount,
            $data->items[0]->contentDetails->relatedPlaylists->uploads
        );
    }

    public function __construct(string $id, string $name, ?string $image, ?int $subscribers, ?string $uploadsId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
        $this->subscribers = $subscribers;
        $this->uploadsId = $uploadsId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getSubscribers(): int
    {
        return $this->subscribers;
    }

    public function getUploadsId(): string
    {
        return $this->uploadsId;
    }
}
