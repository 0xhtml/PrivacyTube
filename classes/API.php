<?php

class API
{

    private const URL = "https://www.googleapis.com/youtube/v3";
    private $key;
    private $mySQL;

    /**
     * YouTube API constructor
     * @param string $key Google API key
     * @param MySQL $mySQL
     */
    public function __construct(string $key, MySQL $mySQL)
    {
        $this->key = $key;
        $this->mySQL = $mySQL;
    }

    /**
     * Get a channel from YouTube
     * @param string $id Channel id
     * @return Channel
     */
    public function getChannel(string $id): Channel
    {
        $data = $this->get("/channels", array("id" => $id, "part" => "statistics,snippet,contentDetails"));
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
            die("Can't load channel $id");
        }

        return new Channel(
            $this,
            $this->mySQL,
            $id,
            $data->items[0]->snippet->title,
            "./dl.php?url=" . urlencode($data->items[0]->snippet->thumbnails->default->url),
            $data->items[0]->statistics->subscriberCount,
            $data->items[0]->contentDetails->relatedPlaylists->uploads
        );
    }

    /**
     * Get a video from YouTube
     * @param string $id Video id
     * @return Video
     */
    public function getVideo(string $id): Video
    {
        $data = $this->get("/videos", array("id" => $id, "part" => "statistics,snippet"));
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
            die("Can't load video $id");
        }

        return new Video(
            $this,
            $this->mySQL,
            $id,
            $data->items[0]->snippet->title,
            $data->items[0]->snippet->description,
            new Channel($this, $this->mySQL, $data->items[0]->snippet->channelId, "./dl.php?url=" . urlencode($data->items[0]->snippet->channelTitle), "", 0, ""),
            strtotime($data->items[0]->snippet->publishedAt),
            $data->items[0]->statistics->viewCount,
            $data->items[0]->statistics->likeCount,
            $data->items[0]->statistics->dislikeCount,
            $data->items[0]->snippet->thumbnails->default->url
        );
    }

    /**
     * Execute a request to the YouTube API and return the json decoded response
     * @param string $url URL
     * @param array $params Parameters
     * @param bool $save If set to true, the response gets saved to the MySQL database.
     * @return mixed Json decoded response
     */
    public function get(string $url, array $params, bool $save = true)
    {
        $params_json = json_encode($params);
        $result = $this->mySQL->execute("SELECT * FROM cache WHERE url = ? AND params = ? AND date > (CURRENT_TIMESTAMP - INTERVAL 2 HOUR) LIMIT 1", "ss", $url, $params_json);
        if ($result->num_rows === 1) {
            return json_decode($result->fetch_object()->data);
        }

        $params["key"] = $this->key;
        $full_url = self::URL . $url . "?" . http_build_query($params);

        $curl = curl_init($full_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);

        if ($save) {
            $this->mySQL->execute("INSERT INTO cache(url, params, data) VALUES (?, ?, ?)", "sss", $url, $params_json, $data);
        }

        return json_decode($data);
    }
}
