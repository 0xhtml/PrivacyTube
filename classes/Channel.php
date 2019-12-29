<?php
require_once "System.php";

class Channel
{
    private $id;
    private $name;
    private $image;
    private $uploadsId;

    public static function fromId(string $id, System $system)
    {
        $result = $system->mysql("SELECT * FROM channels WHERE id = ?", "s", $id);
        if ($result->num_rows === 1) {
            $data = $result->fetch_object();
            return new Channel(
                $id,
                $data->name,
                $data->image,
                $data->uploadsId
            );
        }

        $data = $system->api("/channels", array("id" => $id, "part" => "snippet,contentDetails"));
        if (!isset(
            $data->items,
            $data->items[0],
            $data->items[0]->snippet,
            $data->items[0]->snippet->title,
            $data->items[0]->snippet->thumbnails,
            $data->items[0]->snippet->thumbnails->default,
            $data->items[0]->snippet->thumbnails->default->url,
            $data->items[0]->contentDetails,
            $data->items[0]->contentDetails->relatedPlaylists,
            $data->items[0]->contentDetails->relatedPlaylists->uploads
        )) {
            die("Can't load Channel from id ($id)");
        }

        $system->mysql(
            "INSERT INTO channels(id, name, image, uploadsId) VALUES (?, ?, ?, ?)",
            "ssss",
            $id,
            $data->items[0]->snippet->title,
            $data->items[0]->snippet->thumbnails->default->url,
            $data->items[0]->contentDetails->relatedPlaylists->uploads
        );

        return new Channel(
            $id,
            $data->items[0]->snippet->title,
            $data->items[0]->snippet->thumbnails->default->url,
            $data->items[0]->contentDetails->relatedPlaylists->uploads
        );
    }

    public static function fromQuery(string $q, System $system, int $max = 50): array
    {
        $result = array();

        $channels = $system->api("/search", array("q" => $q, "part" => "snippet", "type" => "channel", "maxResults" => $max));
        if (!isset($channels->items)) {
            die("Can't load searched channels");
        }

        foreach ($channels->items as $channel) {
            if (!isset(
                $channel->snippet,
                $channel->snippet->channelId,
                $channel->snippet->channelTitle,
                $channel->snippet->thumbnails,
                $channel->snippet->thumbnails->default,
                $channel->snippet->thumbnails->default->url
            )) {
                die("Can't load searched channel");
            }
    
            $result[] = new Channel(
                $channel->snippet->channelId,
                $channel->snippet->channelTitle,
                $channel->snippet->thumbnails->default->url,
                null
            );
        }

        return $result;
    }

    public function __construct(string $id, ?string $name, ?string $image, ?string $uploadsId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
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

    public function getUploadsId(): string
    {
        return $this->uploadsId;
    }
}
