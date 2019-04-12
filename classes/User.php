<?php

class User
{
    /**
     * User constructor: Check if the user is logged in if not redirect the user to the login page
     */
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE and isset($_COOKIE["PHPSESSID"])) {
            session_start();
        }
        if (!isset($_SESSION["user"])) {
            header("Location: ./login.php");
            die();
        }
    }

    /**
     * Login a user and redirect the user to the start page. If the username or password is wrong return false.
     * @param MySQL $mySQL
     * @param string $username
     * @param string $password
     * @return bool
     */
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

    /**
     * Register a new user, log the new user in and redirect to the start page. If the username already exists return false.
     * @param MySQL $mySQL
     * @param string $username
     * @param string $password
     * @return bool
     */
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

    /**
     * Check if user is already logged in and if so then redirect the user to the start page.
     */
    public static function checkLogin()
    {
        if (session_status() == PHP_SESSION_NONE and isset($_COOKIE["PHPSESSID"])) {
            session_start();
        }
        if (isset($_SESSION["user"])) {
            header("Location: .");
            die();
        }
    }

    public function logout()
    {
        session_destroy();
    }

    /**
     * Get the current logged in users username as a sha256 string
     * @return mixed
     */
    public function getUser()
    {
        return $_SESSION["user"];
    }
}
