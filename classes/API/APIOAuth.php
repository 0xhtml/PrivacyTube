<?php

class APIOAuth
{

    private const URL = "https://www.googleapis.com/youtube/v3";
    private const OAUTH_AUTH_URL = "https://accounts.google.com/o/oauth2/auth";
    private const OAUTH_TOKEN_URL = "https://www.googleapis.com/oauth2/v4/token";
    private $access_token;

    public function __construct(string $access_token)
    {
        $this->access_token = $access_token;
    }

    public static function redirect(string $client_id, string $redirect_uri)
    {
        $url = self::OAUTH_AUTH_URL . "?" . http_build_query(array(
                "client_id" => $client_id,
                "redirect_uri" => $redirect_uri,
                "scope" => "https://www.googleapis.com/auth/youtube.readonly",
                "access_type" => "online",
                "response_type" => "code"
            ));
        header("Location: $url");
        die();
    }

    public static function redirect_code(string $client_id, string $client_secret, string $code, string $redirect_uri)
    {
        $params = array(
            "client_id" => $client_id,
            "client_secret" => $client_secret,
            "code" => $code,
            "redirect_uri" => $redirect_uri,
            "grant_type" => "authorization_code"
        );
        $curl = curl_init(self::OAUTH_TOKEN_URL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        $data = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($data);
        setcookie("token", $json->access_token, time() + $json->expires_in, "/");
        header("Location: $redirect_uri");
        die();
    }

    public function get(string $url, array $params)
    {
        $params["access_token"] = $this->access_token;
        $curl = curl_init(self::URL . $url . "?" . http_build_query($params));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);
        return json_decode($data);
    }

}
