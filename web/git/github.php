<?php
$key_file = fopen("../../key.txt", "r");
$key = str_replace(["\n", "\r"], "", fgets($key_file));
fclose($key_file);

$signature = $_SERVER['HTTP_X_HUB_SIGNATURE'];
$event = $_SERVER['HTTP_X_GITHUB_EVENT'];
$delivery = $_SERVER['HTTP_X_GITHUB_DELIVERY'];
if (!isset($signature, $event, $delivery)) {
    die();
}
if ($event !== "push") {
    die();
}
$payload = file_get_contents("php://input");
if (strpos($payload, 'payload=') === 0) {
    $payload = substr(urldecode($payload), 8);
}
list($algo, $hash) = explode("=", $signature);
$payload_hash = hash_hmac($algo, $payload, $key);
if ($payload_hash !== $hash) {
    die();
}
exec("cd .. && git checkout . && git pull");
echo "Success!";
