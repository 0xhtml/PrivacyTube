<?php
require_once "classes/Config.php";
require_once "classes/MySQL.php";

Config::setup();
$config = new Config("");

$mySQL = new MySQL($config);
$mySQL->execute("create table if not exists cache( id int auto_increment primary key, url varchar(255) null, params text null, data mediumtext null, date timestamp default CURRENT_TIMESTAMP null)");
$mySQL->execute("create table if not exists subscriptions( id int auto_increment primary key, user varchar(64) null, channel varchar(24) null)");
$mySQL->execute("create table if not exists users( id int auto_increment primary key, username varchar(64) null, password varchar(64) null)");
$mySQL->execute("create table if not exists video_progress( id int auto_increment primary key, video varchar(11) null, user varchar(64) null, progress float null)");
die("Setup success full");
