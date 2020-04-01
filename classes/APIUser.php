<?php
require_once "Main.php";
require_once "User.php";

class APIUser extends User
{
    public function __construct(Main $main)
    {
        $this->loggedin = false;
        $users = $main->mysql("SELECT * FROM users WHERE SHA1(CONCAT(username, password))=?", "s", $_GET["key"]);
        if ($users->num_rows == 0) {
            die("Invalid API-Key");
        }
        $this->user = $users->fetch_assoc()["sql_id"];
    }
}
