<?php

class User
{

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION["user"])) {
            header("Location: ./login");
            die();
        }
    }

    public static function login(mysqli $mysqli, string $username, string $password)
    {
        $password = hash("sha256", $password);

        $statement = $mysqli->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $statement->bind_param("ss", $username, $password);
        if (!$statement->execute()) {
            die("Can't load user: $statement->error");
        }
        $result = $statement->get_result();
        if ($result->num_rows !== 1) {
            return false;
        }

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION["user"] = $username;
        return new self();
    }

    public function get_user()
    {
        return $_SESSION["user"];
    }

}
