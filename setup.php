<?php
require_once "classes/System.php";

System::setupConfig();
$system = new System("");

$system->mysql("create table if not exists cache( id int auto_increment primary key, url text null, params text null, data mediumtext null, date timestamp default CURRENT_TIMESTAMP null)");
$system->mysql("create table if not exists channels( id int auto_increment primary key, cid varchar(24) null, name text null, image text null, subscribers int null, uploadsId varchar(24) null, date timestamp default CURRENT_TIMESTAMP null)");
$system->mysql("create table if not exists subscriptions( id int auto_increment primary key, user varchar(64) null, channel varchar(24) null)");
$system->mysql("create table if not exists users( id int auto_increment primary key, username varchar(64) null, password varchar(64) null, donotdisturb int(1) null)");
$system->mysql("create table if not exists videos( id int auto_increment primary key, vid varchar(11) null, title text null, description text null, channel varchar(24) null, vdate timestamp null, views int null, likes int null, dislikes int null, thumbnail text null, date timestamp default CURRENT_TIMESTAMP null)");
die("Setup success full");
