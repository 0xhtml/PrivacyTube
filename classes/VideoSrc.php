<?php

class VideoSrc
{
    private $url;

    public static function fromId(string $id): array
    {
        $response = file_get_contents("https://www.youtube.com/get_video_info?video_id=" . urlencode($id));
        parse_str($response, $data);
        if (!isset($data["player_response"])) {
            die("Can't load VideoSrc from id ($id)");
        }
        $playerdata = json_decode($data["player_response"]);
        if (!isset($playerdata->streamingData) or !isset($playerdata->streamingData->formats) or !is_array($playerdata->streamingData->formats)) {
            die("Can't load VideoSrc from id ($id)");
        }
        $result = array();
        foreach ($playerdata->streamingData->formats as $src) {
            if (isset($src->url)) {
                $result[] = new self($src->url);
            }
        }
        return $result;
    }

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
