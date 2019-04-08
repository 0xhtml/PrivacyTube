<?php
class APIOAuth extends API {

    private const OAUTH_URL = "https://accounts.google.com/o/oauth2/v2";

    public static function redirect(string $client_id, string $redirect_uri) {
        $url = self::build_url(self::OAUTH_URL. "/auth", array(
            "client_id" => $client_id,
            "redirect_uri" => $redirect_uri,
            "scope" => "https://www.googleapis.com/auth/youtube.readonly",
            "access_type" => "online",
            "response_type" => "code"
        ));
        header("Location: " . $url);
        die();
    }

    public function __construct(string $client_id, string $client_secret, string $code, string $redirect_uri){
        $url = self::build_url(self::OAUTH_URL . "/token", array(
            "client_id" => $client_id,
            "client_secret" => $client_secret,
            "code" => $code,
            "redirect_uri" => $redirect_uri,
            "grant_type" => "authorization_code"
        ));
        $curl= curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($data);
        setcookie("token", $json->access_token, time() + 60 * 60 * 24 * 10000, "/");
        parent::__construct($json->access_token);
    }

}
