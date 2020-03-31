<?php
require_once "Main.php";

class User
{
    private $loggedin;
    private $user;

    public static function login(string $username, string $password, Main $main): bool
    {
        $username = hash("sha256", $username);
        $password = hash("sha256", $password);

        $result = $main->mysql("SELECT * FROM users WHERE username = ? AND password = ?", "ss", $username, $password);
        if ($result->num_rows !== 1) {
            return false;
        }

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION["user"] = $result->fetch_object()->sql_id;
        header("Location: .");
        die();
    }

    public static function register(string $username, string $password, Main $main): bool
    {
        $username = hash("sha256", $username);
        $password = hash("sha256", $password);

        $result = $main->mysql("SELECT * FROM users WHERE username = ?", "s", $username);
        if ($result->num_rows !== 0) {
            return false;
        }

        $result = $main->mysql("INSERT INTO users(username, password) VALUES (?, ?)", "ss", $username, $password);

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION["user"] = $result->insert_id;
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
            $this->user = $_SESSION["user"];
        } else {
            $this->loggedin = false;
            if ($redirect) {
                header("Location: ./login.php");
                die();
            }
        }
    }

    public function subscribe(Channel $channel, Main $main)
    {
        $main->mysql("INSERT INTO subscriptions(user, channel) VALUES (?, ?)", "is", $this->user, $channel->getId());
    }

    public function unsubscribe(Channel $channel, Main $main)
    {
        $main->mysql("DELETE FROM subscriptions WHERE user = ? AND channel = ?", "is", $this->user, $channel->getId());
    }

    public function isSubscribed(Channel $channel, Main $main): bool
    {
        $result = $main->mysql("SELECT * FROM subscriptions WHERE user = ? AND channel = ?", "is", $this->user, $channel->getId());
        return $result->num_rows === 1;
    }

    public function logout()
    {
        session_destroy();
    }

    public function getSubscriptions(Main $main): array
    {
        $subscriptions = array();
        $result = $main->mysql("SELECT * FROM subscriptions WHERE user = ?", "i", $this->user);
        while ($row = $result->fetch_object()) {
            $subscriptions[] = $row->channel;
        }
        return $subscriptions;
    }

    public function getLoggedin(): bool
    {
        return $this->loggedin;
    }

    public function getUser(): string
    {
        return $this->user;
    }
}
