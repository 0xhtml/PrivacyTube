<?php
class API {

    private const URL = "https://www.googleapis.com/youtube/v3";
    private $key;

    public function __construct($key) {
        $this->key = $key;
    }

    public function get(string $url, array $params) {
        $params["key"] = $this->key;
        $curl= curl_init($this->build_url(self::URL . $url, $params));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);
        return json_decode($data);
    }

    public static function build_url(string $url, array $params) {
        $first = "true";
        foreach ($params as $key => $value) {
            if ($first) {
                $first = false;
                $url .= "?";
            } else {
                $url .= "&";
            }
            $url .= $key . "=" . $value;
        }
        return $url;
    }

}
