<?php

class VideoSrc
{
    private $frame;
    private $src;

    public static function fromId(string $id): self
    {
        $curl = curl_init("https://www.youtube.com/watch?v=" . urlencode($id) . "&pbj=1");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "X-YouTube-Client-Name: 1",
            "X-YouTube-Client-Version: 2.20190926.06.01"
        ));
        $data = json_decode(curl_exec($curl));
        curl_close($curl);

        $srcs = array();
        foreach ($data as $value) {
            if (!isset(
                $value->player,
                $value->player->args,
                $value->player->args->player_response
            )) {
                continue;
            }

            $player_data = json_decode($value->player->args->player_response);
            if (!isset(
                $player_data->streamingData,
                $player_data->streamingData->formats,
                $player_data->streamingData->adaptiveFormats
            )) {
                continue;
            }

            foreach ($player_data->streamingData->formats as $src) {
                if (isset($src->url)) {
                    $srcs[] = $src->url;
                }
            }
        }
        if (count($srcs) > 0) {
            return new self(false, $srcs);
        } else {
            return new self(true, "");
        }
    }

    public function __construct(bool $frame, $src)
    {
        $this->frame = $frame;
        $this->src = $src;
    }

    public function getHtml(): string
    {
        if ($this->frame) {
            return "iframe";
        } else {
            $html = "<video autoplay controls>";
            foreach ($this->src as $value) {
                $html .= "<source src=\"$value\">";
            }
            $html .= "</video>";
            return $html;
        }
    }
}
