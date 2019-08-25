<?php

class System
{
    private const API_URL = "https://www.googleapis.com/youtube/v3";
    public const API_REGIONS = array("DZ", "AR", "AU", "AT", "AZ", "BH", "BY", "BE", "BO", "BA", "BR", "BG", "CA", "CL", "CO", "CR", "HR", "CY", "CZ", "DK", "DO", "EC", "EG", "SV", "EE", "FI", "FR", "GE", "DE", "GH", "GR", "GT", "HN", "HK", "HU", "IS", "IN", "ID", "IQ", "IE", "IL", "IT", "JM", "JP", "JO", "KZ", "KE", "KW", "LV", "LB", "LY", "LI", "LT", "LU", "MY", "MT", "MX", "ME", "MA", "NP", "NL", "NZ", "NI", "NG", "MK", "NO", "OM", "PK", "PA", "PY", "PE", "PH", "PL", "PT", "PR", "QA", "RO", "RU", "SA", "SN", "RS", "SG", "SK", "SI", "ZA", "KR", "ES", "LK", "SE", "CH", "TW", "TZ", "TH", "TN", "TR", "UG", "UA", "AE", "GB", "US", "UY", "VN", "YE", "ZW");
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
        return $result;
    }

    public function api(string $url, array $params, bool $save = true)
    {
        $params_json = json_encode($params);
        $result = $this->mysql("SELECT * FROM cache WHERE url = ? AND params = ? AND date > (CURRENT_TIMESTAMP - INTERVAL 1 HOUR) LIMIT 1", "ss", $url, $params_json);
        if ($result->num_rows === 1) {
            return json_decode($result->fetch_object()->data);
        }

        $params["key"] = $this->config("api_key");
        $full_url = self::API_URL . $url . "?" . http_build_query($params);

        $curl = curl_init($full_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);

        if ($save) {
            $this->mysql("INSERT INTO cache(url, params, data) VALUES (?, ?, ?)", "sss", $url, $params_json, $data);
        }

        return json_decode($data);
    }
}
