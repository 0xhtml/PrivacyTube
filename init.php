<?php
require_once "classes/Template.php";

require_once "classes/API/API.php";
require_once "classes/API/APIVideo.php";

$key_file = fopen("key.txt", "r");
$key = fread($key_file, filesize($key_file));
fclose($key_file);
$API = new API($key);
