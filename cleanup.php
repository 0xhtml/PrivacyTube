<?php
$key_file = fopen("key.txt", "r");
$key = str_replace(["\n", "\r"], "", fgets($key_file));
$mysql_pass = str_replace(["\n", "\r"], "", fgets($key_file));
fclose($key_file);
$mysqli = mysqli_connect("localhost", "PrivacyTube", $mysql_pass, "PrivacyTube");
$statement = $mysqli->prepare("DELETE FROM cache WHERE date < (CURRENT_TIMESTAMP - INTERVAL 2 HOUR - INTERVAL 5 MINUTE)");
if ($statement->execute()) {
    echo "Success!";
} else {
    echo "Error: $statement->error";
}
