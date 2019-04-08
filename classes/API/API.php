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

    public function get(string $url, array $params, bool $save = false)
    {
        $params["key"] = $this->key;
        $url = self::URL . $url . "?" . http_build_query($params);

        $statement = $this->mysqli->prepare("SELECT * FROM cache WHERE url = ? ORDER BY date LIMIT 1");
        $statement->bind_param("s", $url);
        if ($statement->execute()) {
            $result = $statement->get_result();
            if ($result->num_rows === 1) {
                return json_decode($result->fetch_object()->data);
            }
        }

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);

        if ($save) {
            $statement = $this->mysqli->prepare("INSERT INTO cache(url, data) VALUES (?, ?)");
            $statement->bind_param("ss", $url, $data);
            $statement->execute();
        }

        return json_decode($data);
    }

}
