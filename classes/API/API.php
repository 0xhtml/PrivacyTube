<?php
class API {

    private const URL = "https://www.googleapis.com/youtube/v3";
    private $key;

    public function __construct($key) {
        $this->key = $key;
    }

    public function get(string $url, array $params) {
        $params["key"] = $this->key;
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
        return json_decode(file_get_contents(self::URL . $url));
    }

}
