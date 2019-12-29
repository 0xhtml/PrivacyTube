<?php

class System
{
    private const API_URL = "https://www.googleapis.com/youtube/v3";
    private const CONFIG_FILE = "config.json";
    private $mysqli;
    private $config;

    public static function setupConfig()
    {
        if (!file_exists(self::CONFIG_FILE)) {
            file_put_contents(self::CONFIG_FILE, json_encode([
                "mysql_host" => "localhost",
                "mysql_user" => "root",
                "mysql_pass" => "",
                "mysql_db" => "PrivacyTube",
                "api_key" => ""
            ]));
        }
    }

    public function __construct(string $configPath = "../")
    {
        if (!file_exists($configPath . self::CONFIG_FILE)) {
            die("Can't find config file");
        }
        $this->config = json_decode(file_get_contents($configPath . self::CONFIG_FILE), true);

        $this->mysqli = mysqli_connect($this->config("mysql_host"), $this->config("mysql_user"), $this->config("mysql_pass"), $this->config("mysql_db"));
        if (!$this->mysqli) {
            die("Can't connect to MySQL: " . mysqli_connect_error());
        }
    }

    public function config(string $var)
    {
        return $this->config[$var];
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
        $params["key"] = $this->config("api_key");
        $full_url = self::API_URL . $url . "?" . http_build_query($params);

        $curl = curl_init($full_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);

        return json_decode($data);
    }
}
