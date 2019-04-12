<?php
include_once "../init.php";
$user = new User();
$user->logout();
header("Location: .");
die();
