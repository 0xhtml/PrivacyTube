<?php

class User
{
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION["user"])) {
            header("Location: ./login.php");
            die();
        }
    }

    public static function login(MySQL $mySQL, string $username, string $password)
    {
        $username = hash("sha256", $username);
        $password = hash("sha256", $password);

        $result = $mySQL->execute("SELECT * FROM users WHERE username = ? AND password = ?", "ss", $username, $password);
        if ($result->num_rows !== 1) {
            return false;
        }

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION["user"] = $username;
        header("Location: .");
        die();
    }

    public function getUser()
    {
        return $_SESSION["user"];
    }
}
