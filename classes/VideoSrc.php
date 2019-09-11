<?php

class VideoSrc
{
    private $src;

    public static function fromId(string $id): self
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
        $qualities = array("hd1080", "hd720", "medium");
        foreach ($qualities as $quality) {
            foreach ($playerdata->streamingData->formats as $src) {
                if ($src->quality == $quality) {
                    return new self($src->url);
                }
            }
        }
    }

    public function __construct(string $src)
    {
        $this->src = $src;
    }

    public function getSrc(): string
    {
        return $this->src;
    }
}
