<?php

class VideoSrc
{
    private $src;

    public static function fromId(string $id)
    {
        $response = file_get_contents("https://www.youtube.com/get_video_info?ps=maxres&video_id=" . urlencode($id));
        parse_str($response, $data);
        if (!isset($data["url_encoded_fmt_stream_map"])) {
            die("Can't load VideoSrc from id ($id)");
        }
        parse_str($data["url_encoded_fmt_stream_map"], $urldata);
        if (!isset($urldata["url"])) {
            die("Can't load VideoSrc from id ($id)");
        }
        return new self($urldata["url"]);
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
