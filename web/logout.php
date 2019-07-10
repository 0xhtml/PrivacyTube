<?php
require_once "../classes/User.php";
$user = new User();
$user->logout();
header("Location: .");
die();
