<?php
class API {

    private const URL = "https://www.googleapis.com/youtube/v3";
    private $key;
    private $mysqli;

    public function __construct(string $key, mysqli $mysqli)
    {
        $this->key = $key;
        $this->mysqli = $mysqli;
    }

    public function get(string $url, array $params, bool $save = true)
    {
        $statement = $this->mysqli->prepare("SELECT * FROM cache WHERE url = ? AND params = ? AND date > (CURRENT_TIMESTAMP - INTERVAL 2 HOUR) LIMIT 1");
        $params_json = json_encode($params);
        $statement->bind_param("ss", $url, $params_json);
        if ($statement->execute()) {
            $result = $statement->get_result();
            if ($result->num_rows === 1) {
                return json_decode($result->fetch_object()->data);
            }
        } else {
            debug("Loading from cache failed: $statement->error");
        }

        $params["key"] = $this->key;
        $full_url = self::URL . $url . "?" . http_build_query($params);

        $curl = curl_init($full_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);

        if ($save) {
            $statement = $this->mysqli->prepare("INSERT INTO cache(url, params, data) VALUES (?, ?, ?)");
            $statement->bind_param("sss", $url, $params_json, $data);
            if (!$statement->execute()) {
                debug("Saving to cache failed: $statement->error");
            }
        }

        return json_decode($data);
    }

}
