<?php
require_once "System.php";

class User
{
    private $loggedin;
    private $user;

    public static function login(string $username, string $password, System $system): bool
    {
        $username = hash("sha256", $username);
        $password = hash("sha256", $password);

        $result = $system->mysql("SELECT * FROM users WHERE username = ? AND password = ?", "ss", $username, $password);
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

    public static function register(string $username, string $password, System $system): bool
    {
        $username = hash("sha256", $username);
        $password = hash("sha256", $password);

        $result = $system->mysql("SELECT * FROM users WHERE username = ?", "s", $username);
        if ($result->num_rows !== 0) {
            return false;
        }

        $result = $system->mysql("INSERT INTO users(username, password) VALUES (?, ?)", "ss", $username, $password);

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

    public function subscribe(Channel $channel, System $system)
    {
        $system->mysql("INSERT INTO subscriptions(user, channel) VALUES (?, ?)", "is", $this->user, $channel->getId());
    }

    public function unsubscribe(Channel $channel, System $system)
    {
        $system->mysql("DELETE FROM subscriptions WHERE user = ? AND channel = ?", "is", $this->user, $channel->getId());
    }

    public function isSubscribed(Channel $channel, System $system): bool
    {
        $result = $system->mysql("SELECT * FROM subscriptions WHERE user = ? AND channel = ?", "is", $this->user, $channel->getId());
        return $result->num_rows === 1;
    }

    public function logout()
    {
        session_destroy();
    }

    public function getSubscriptions(System $system): array
    {
        $subscriptions = array();
        $result = $system->mysql("SELECT * FROM subscriptions WHERE user = ?", "i", $this->user);
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
