<?php
require_once "System.php";

class User
{
    private $loggedin;
    private $username;
    private $donotdisturb;

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
        $_SESSION["user"] = $username;
        $_SESSION["donotdisturb"] = $result->fetch_object()->donotdisturb;
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

        $system->mysql("INSERT INTO users(username, password, donotdisturb) VALUES (?, ?, 0)", "ss", $username, $password);

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION["user"] = $username;
        $_SESSION["donotdisturb"] = 0;
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
            $this->donotdisturb = $_SESSION["donotdisturb"];
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
        $system->mysql("INSERT INTO subscriptions(user, channel) VALUES (?, ?)", "ss", $this->username, $channel->getId());
    }

    public function unsubscribe(Channel $channel, System $system)
    {
        $system->mysql("DELETE FROM subscriptions WHERE user = ? AND channel = ?", "ss", $this->username, $channel->getId());
    }

    public function isSubscribed(Channel $channel, System $system): bool
    {
        $result = $system->mysql("SELECT * FROM subscriptions WHERE user = ? AND channel = ?", "ss", $this->username, $channel->getId());
        return $result->num_rows === 1;
    }

    public function logout()
    {
        session_destroy();
    }

    public function getSubscriptions(System $system): array
    {
        $subscriptions = array();
        $result = $system->mysql("SELECT * FROM subscriptions WHERE user = ?", "s", $this->username);
        while ($row = $result->fetch_object()) {
            $subscriptions[] = $row->channel;
        }
        return $subscriptions;
    }

    public function getLoggedin(): bool
    {
        return $this->loggedin;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getDonotdisturb(): int
    {
        return $this->donotdisturb;
    }

    public function getDonotdisturbBool(): bool
    {
        return ($this->loggedin and $this->donotdisturb === 1);
    }
}
