<?php
if (!file_exists("key.txt")) {
    die("Can't find key file");
}

$key_file = fopen("key.txt", "r");
$key = str_replace(["\n", "\r"], "", fgets($key_file));
$mysql_host = str_replace(["\n", "\r"], "", fgets($key_file));
$mysql_pass = str_replace(["\n", "\r"], "", fgets($key_file));
fclose($key_file);

$mysqli = mysqli_connect("localhost", "PrivacyTube", $mysql_pass, "PrivacyTube");
if (!$mysqli) {
    die("Can't connect to MySQL: " . mysqli_connect_error());
}

$statement = $mysqli->prepare("DELETE FROM cache WHERE date < (CURRENT_TIMESTAMP - INTERVAL 2 HOUR - INTERVAL 5 MINUTE)");
if ($statement->execute()) {
    echo "Success!\nDeleted $statement->affected_rows cache entries\n";
} else {
    echo "Error: $statement->error\n";
}
