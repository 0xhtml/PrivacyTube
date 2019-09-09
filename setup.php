<?php
require_once "classes/System.php";

System::setupConfig();
$system = new System("");

$system->mysql("create table if not exists cache( id int auto_increment primary key, url text null, params text null, data mediumtext null, date timestamp default CURRENT_TIMESTAMP null)");
$system->mysql("create table if not exists channels( sql_id int auto_increment primary key, sql_date timestamp default CURRENT_TIMESTAMP null, id varchar(24), name text, image text, uploadsId varchar(24))");
$system->mysql("create table if not exists subscriptions( id int auto_increment primary key, user varchar(64) null, channel varchar(24) null)");
$system->mysql("create table if not exists users( id int auto_increment primary key, username varchar(64) null, password varchar(64) null, donotdisturb int(1) null)");
$system->mysql("create table if not exists videos( sql_id int auto_increment primary key, sql_date timestamp default CURRENT_TIMESTAMP, sql_state int,id varchar(11), title text, description text, channel varchar(24), date timestamp null, thumbnail text)");
die("Setup success full");
