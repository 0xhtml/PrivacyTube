<?php
require_once "classes/System.php";

System::setupConfig();
$system = new System("");

$system->mysql("create table if not exists cache( id int auto_increment primary key, url varchar(255) null, params text null, data mediumtext null, date timestamp default CURRENT_TIMESTAMP null)");
$system->mysql("create table if not exists subscriptions( id int auto_increment primary key, user varchar(64) null, channel varchar(24) null)");
$system->mysql("create table if not exists users( id int auto_increment primary key, username varchar(64) null, password varchar(64) null, donotdisturb int(1) null)");
die("Setup success full");
