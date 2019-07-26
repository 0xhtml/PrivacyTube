<?php

class API
{
    private const URL = "https://www.googleapis.com/youtube/v3";
    public const REGIONS = array("DZ", "AR", "AU", "AT", "AZ", "BH", "BY", "BE", "BO", "BA", "BR", "BG", "CA", "CL", "CO", "CR", "HR", "CY", "CZ", "DK", "DO", "EC", "EG", "SV", "EE", "FI", "FR", "GE", "DE", "GH", "GR", "GT", "HN", "HK", "HU", "IS", "IN", "ID", "IQ", "IE", "IL", "IT", "JM", "JP", "JO", "KZ", "KE", "KW", "LV", "LB", "LY", "LI", "LT", "LU", "MY", "MT", "MX", "ME", "MA", "NP", "NL", "NZ", "NI", "NG", "MK", "NO", "OM", "PK", "PA", "PY", "PE", "PH", "PL", "PT", "PR", "QA", "RO", "RU", "SA", "SN", "RS", "SG", "SK", "SI", "ZA", "KR", "ES", "LK", "SE", "CH", "TW", "TZ", "TH", "TN", "TR", "UG", "UA", "AE", "GB", "US", "UY", "VN", "YE", "ZW");
    private $config;
    private $mySQL;

    /**
     * YouTube API constructor
     * @param Config $config
     * @param MySQL $mySQL
     */
    public function __construct(Config $config, MySQL $mySQL)
    {
        $this->config = $config;
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
            $data->items[0]->snippet->thumbnails->default->url,
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
            $data->items[0]->snippet->thumbnails,
            $data->items[0]->snippet->thumbnails->medium,
            $data->items[0]->snippet->thumbnails->medium->url,
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
            Video::idToURL($id),
            $data->items[0]->snippet->title,
            $data->items[0]->snippet->description,
            new Channel($this, $this->mySQL, $data->items[0]->snippet->channelId, $data->items[0]->snippet->channelTitle, "", 0, ""),
            strtotime($data->items[0]->snippet->publishedAt),
            $data->items[0]->statistics->viewCount,
            $data->items[0]->statistics->likeCount,
            $data->items[0]->statistics->dislikeCount,
            $data->items[0]->snippet->thumbnails->medium->url
        );
    }

    /**
     * Get the YouTube Trends
     * @param string $region The region for the trends (e.g. us)
     * @param int $count The number of videos to get
     * @return array
     */
    public function getTrends(string $region, int $count): array
    {
        $result = array();

        $data = $this->get("/videos", array("chart" => "mostPopular", "regionCode" => $region, "maxResults" => $count, "part" => "statistics,snippet"));
        if (!isset($data->items)) {
            die("Can't load trends of $region");
        }

        foreach ($data->items as $video) {
            if (!isset(
                $video->snippet,
                $video->snippet->title,
                $video->snippet->description,
                $video->snippet->channelId,
                $video->snippet->publishedAt,
                $video->snippet->thumbnails,
                $video->snippet->thumbnails->medium,
                $video->snippet->thumbnails->medium->url,
                $video->statistics,
                $video->statistics->viewCount,
                $video->statistics->likeCount,
                $video->statistics->dislikeCount
            )) {
                die("Can't load video of trends $region");
            }

            $result[] = new Video(
                $this,
                $this->mySQL,
                $video->id,
                "",
                $video->snippet->title,
                $video->snippet->description,
                new Channel($this, $this->mySQL, $video->snippet->channelId, $video->snippet->channelTitle, "", 0, ""),
                strtotime($video->snippet->publishedAt),
                $video->statistics->viewCount,
                $video->statistics->likeCount,
                $video->statistics->dislikeCount,
                $video->snippet->thumbnails->medium->url
            );
        }

        return $result;
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
        $result = $this->mySQL->execute("SELECT * FROM cache WHERE url = ? AND params = ? AND date > (CURRENT_TIMESTAMP - INTERVAL 1 HOUR) LIMIT 1", "ss", $url, $params_json);
        if ($result->num_rows === 1) {
            return json_decode($result->fetch_object()->data);
        }

        $params["key"] = $this->config->getAPIKey();
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
