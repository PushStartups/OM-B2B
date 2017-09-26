<?php

require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");

$tags   =   DB::queryFirstRow("SELECT * FROM tags where name_en = '".$_POST['name_en']."' ");

echo json_encode($tags['name_he']);