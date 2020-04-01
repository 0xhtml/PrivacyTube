<?php
require_once "../../classes/APIUser.php";
require_once "../../classes/Main.php";

$main = new Main();
$user = new APIUser($main);
header("Content-Type: application/json");
echo json_encode($user->getSubscriptions($main));
