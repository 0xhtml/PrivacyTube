<?php

class VideoSrc
{
    private $url;

    public static function fromId(string $id): array
    {
        $curl = curl_init("https://www.youtube.com/watch?v=" . urlencode($id) . "&pbj=1");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "X-YouTube-Client-Name: 1",
            "X-YouTube-Client-Version: 2.20190926.06.01"
        ));
        $data = json_decode(curl_exec($curl));
        curl_close($curl); 

        $result = array();
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
                    $result[] = new self($src->url);
                }
            }
            foreach ($player_data->streamingData->adaptiveFormats as $src) {
                if (isset($src->url)) {
                    $result[] = new self($src->url);
                }
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
