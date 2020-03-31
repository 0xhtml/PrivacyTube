<?php

require_once "classes/Main.php";

$main = new Main();

$main->mysql("CREATE TABLE IF NOT EXISTS channels(
    sql_id int AUTO_INCREMENT PRIMARY KEY,
    sql_date timestamp DEFAULT CURRENT_TIMESTAMP,
    id varchar(24),
    name text,
    image text,
    uploadsId varchar(24)
)");
$main->mysql("CREATE TABLE IF NOT EXISTS users(
    sql_id int AUTO_INCREMENT PRIMARY KEY,
    username varchar(64),
    password varchar(64)
)");
$main->mysql("CREATE TABLE IF NOT EXISTS subscriptions(
    sql_id int AUTO_INCREMENT PRIMARY KEY,
    user int,
    channel varchar(24),
    FOREIGN KEY (user) REFERENCES users(sql_id) ON DELETE CASCADE ON UPDATE CASCADE
)");
$main->mysql("CREATE TABLE IF NOT EXISTS videos(
    sql_id int AUTO_INCREMENT PRIMARY KEY,
    sql_date timestamp DEFAULT CURRENT_TIMESTAMP,
    id varchar(11) UNIQUE,
    title text,
    description text,
    channel varchar(24),
    date timestamp null,
    thumbnail text
)");
$main->mysql("CREATE TABLE IF NOT EXISTS ai(
    sql_id int AUTO_INCREMENT PRIMARY KEY,
    user int,
    id varchar(11),
    title text,
    channel varchar(24),
    channelname text,
    thumbnail text,
    eval int,
    FOREIGN KEY (user) REFERENCES users(sql_id) ON DELETE CASCADE ON UPDATE CASCADE
)");
