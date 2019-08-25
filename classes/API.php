<?php

class API
{
    private const URL = "https://www.googleapis.com/youtube/v3";
    public const REGIONS = array("DZ", "AR", "AU", "AT", "AZ", "BH", "BY", "BE", "BO", "BA", "BR", "BG", "CA", "CL", "CO", "CR", "HR", "CY", "CZ", "DK", "DO", "EC", "EG", "SV", "EE", "FI", "FR", "GE", "DE", "GH", "GR", "GT", "HN", "HK", "HU", "IS", "IN", "ID", "IQ", "IE", "IL", "IT", "JM", "JP", "JO", "KZ", "KE", "KW", "LV", "LB", "LY", "LI", "LT", "LU", "MY", "MT", "MX", "ME", "MA", "NP", "NL", "NZ", "NI", "NG", "MK", "NO", "OM", "PK", "PA", "PY", "PE", "PH", "PL", "PT", "PR", "QA", "RO", "RU", "SA", "SN", "RS", "SG", "SK", "SI", "ZA", "KR", "ES", "LK", "SE", "CH", "TW", "TZ", "TH", "TN", "TR", "UG", "UA", "AE", "GB", "US", "UY", "VN", "YE", "ZW");
    private $config;
    private $mySQL;

    public function __construct(Config $config, MySQL $mySQL)
    {
        $this->config = $config;
        $this->mySQL = $mySQL;
    }

    public function get(string $url, array $params, bool $save = true)
    {
        $params_json = json_encode($params);
        $result = $this->mySQL->execute("SELECT * FROM cache WHERE url = ? AND params = ? AND date > (CURRENT_TIMESTAMP - INTERVAL 1 HOUR) LIMIT 1", "ss", $url, $params_json);
        if ($result->num_rows === 1) {
            return json_decode($result->fetch_object()->data);
        }

        $params["key"] = $this->config->getAPIKey();
        $full_url = self::URL . $url . "?" . http_build_query($params);

        $curl = curl_init($full_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);

        if ($save) {
            $this->mySQL->execute("INSERT INTO cache(url, params, data) VALUES (?, ?, ?)", "sss", $url, $params_json, $data);
        }

        return json_decode($data);
    }
}
