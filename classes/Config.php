<?php

class Config
{
    private const FILE = "config.json";
    private $mysql_host;
    private $mysql_user;
    private $mysql_pass;
    private $mysql_db;
    private $api_key;

    public static function setup()
    {
        if (!file_exists(self::FILE)) {
            file_put_contents(self::FILE, json_encode([
                "mysql_host" => "localhost",
                "mysql_user" => "root",
                "mysql_pass" => "",
                "mysql_db" => "PrivacyTube",
                "api_key" => ""
            ]));
        }
    }

    public function __construct(string $path = "../")
    {
        if (!file_exists($path . self::FILE)) {
            die("Can't find config file");
        }
        $json = json_decode(file_get_contents($path . self::FILE));
        $this->mysql_host = isset($json->mysql_host) ? $json->mysql_host : "localhost";
        $this->mysql_user = isset($json->mysql_user) ? $json->mysql_user : "root";
        $this->mysql_pass = isset($json->mysql_pass) ? $json->mysql_pass : "";
        $this->mysql_db = isset($json->mysql_db) ? $json->mysql_db : "PrivacyTube";
        $this->api_key = isset($json->api_key) ? $json->api_key : "";
    }

    public function getMySQLHost(): String
    {
        return $this->mysql_host;
    }

    public function getMySQLUser(): String
    {
        return $this->mysql_user;
    }

    public function getMySQLPass(): String
    {
        return $this->mysql_pass;
    }

    public function getMySQLDB(): String
    {
        return $this->mysql_db;
    }
    public function getAPIKey(): String
    {
        return $this->api_key;
    }
}
