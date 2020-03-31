<?php

class Main
{
    private const API_URL = "https://www.googleapis.com/youtube/v3";
    private $mysqli;

    public function __construct()
    {
        $this->mysqli = mysqli_connect($_ENV["mysql_host"], $_ENV["mysql_user"], $_ENV["mysql_pass"], $_ENV["mysql_db"]);
        if (!$this->mysqli) {
            die("Can't connect to MySQL: " . mysqli_connect_error());
        }
    }

    public function mysql(string $sql, string $parameter_types = null, ...$parameters)
    {
        if (!($statement = $this->mysqli->prepare($sql))) {
            die("Can't execute SQL \"$sql\": " . $this->mysqli->error);
        }
        if ($parameter_types != null) {
            $statement->bind_param($parameter_types, ...$parameters);
        }
        if (!$statement->execute()) {
            die("Can't execute SQL \"$sql\": $statement->error");
        }
        $result = $statement->get_result();
        if ($result === false) {
            return $statement;
        }
        return $result;
    }

    public function api(string $url, array $params)
    {
        $params["key"] = $_ENV["api_key"];
        $full_url = self::API_URL . $url . "?" . http_build_query($params);

        $curl = curl_init($full_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);

        return json_decode($data);
    }
}
