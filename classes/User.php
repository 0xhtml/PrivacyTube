<?php

class User
{
    private $loggedin;
    private $username;

    public static function login(MySQL $mySQL, string $username, string $password): bool
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

    public static function register(MySQL $mySQL, string $username, string $password): bool
    {
        $username = hash("sha256", $username);
        $password = hash("sha256", $password);

        $result = $mySQL->execute("SELECT * FROM users WHERE username = ?", "s", $username);
        if ($result->num_rows !== 0) {
            return false;
        }

        $mySQL->execute("INSERT INTO users(username, password) VALUES (?, ?)", "ss", $username, $password);

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION["user"] = $username;
        header("Location: .");
        die();
    }

    public function __construct(bool $redirect = false)
    {
        if (session_status() == PHP_SESSION_NONE and isset($_COOKIE["PHPSESSID"])) {
            session_start();
        }
        if (isset($_SESSION["user"])) {
            $this->loggedin = true;
            $this->username = $_SESSION["user"];
        } else {
            $this->loggedin = false;
            if ($redirect) {
                header("Location: ./login.php");
                die();
            }
        }
    }

    public function subsribe(Channel $channel, MySQL $mySQL)
    {
        $mySQL->execute("INSERT INTO subscriptions(user, channel) VALUES (?, ?)", "ss", $this->username, $channel->getId());
    }

    public function unsubsribe(Channel $channel, MySQL $mySQL)
    {
        $mySQL->execute("DELETE FROM subscriptions WHERE user = ? AND channel = ?", "ss", $this->username, $channel->getId());
    }

    public function isSubscribed(Channel $channel, MySQL $mySQL): bool
    {
        $result = $mySQL->execute("SELECT * FROM subscriptions WHERE user = ? AND channel = ?", "ss", $this->username, $channel->getId());
        return $result->num_rows === 1;
    }

    public function getLoggedin(): bool
    {
        return $this->loggedin;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}
