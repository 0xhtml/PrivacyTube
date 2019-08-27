<?php
require_once "System.php";

class Channel
{
    private $id;
    private $name;
    private $image;
    private $subscribers;
    private $uploadsId;

    public static function fromId(string $id, System $system)
    {
        $result = $system->mysql("SELECT * FROM channels WHERE cid = ? AND date > (CURRENT_TIMESTAMP - INTERVAL 2 DAY)", "s", $id);
        if ($result->num_rows === 1) {
            $data = $result->fetch_object();
            return new Channel(
                $id,
                $data->name,
                $data->image,
                $data->subscribers,
                $data->uploadsId
            );
        }

        $data = $system->api("/channels", array("id" => $id, "part" => "statistics,snippet,contentDetails"), false);
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

        $system->mysql(
            "INSERT INTO channels(cid, name, image, subscribers, uploadsId) VALUES (?, ?, ?, ?, ?)",
            "sssis",
            $id,
            $data->items[0]->snippet->title,
            $data->items[0]->snippet->thumbnails->default->url,
            $data->items[0]->statistics->subscriberCount,
            $data->items[0]->contentDetails->relatedPlaylists->uploads
        );

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
