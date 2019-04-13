<?php
require_once "classes/MySQL.php";

if (!file_exists("key.txt")) {
    die("Can't find key file");
}

$key_file = fopen("key.txt", "r");
$key = str_replace(["\n", "\r"], "", fgets($key_file));
$mysql_host = str_replace(["\n", "\r"], "", fgets($key_file));
$mysql_pass = str_replace(["\n", "\r"], "", fgets($key_file));
fclose($key_file);

$mysqli = mysqli_connect($mysql_host, "PrivacyTube", $mysql_pass, "PrivacyTube");
if (!$mysqli) {
    die("Can't connect to MySQL: " . mysqli_connect_error());
}

$mySQL = new MySQL($mysqli);
$mySQL->execute("create table if not exists cache( id int auto_increment primary key, url varchar(255) null, params text null, data mediumtext null, date timestamp default CURRENT_TIMESTAMP null)");
$mySQL->execute("create table if not exists subscriptions( id int auto_increment primary key, user varchar(64) null, channel varchar(24) null)");
$mySQL->execute("create table if not exists users( id int auto_increment primary key, username varchar(64) null, password varchar(64) null)");
die("Setup success full");
