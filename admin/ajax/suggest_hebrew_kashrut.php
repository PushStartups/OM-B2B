<?php

require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");


DB::useDB(B2B_RESTAURANTS);

$kashruts   =   DB::queryFirstRow("SELECT * FROM kashrut where name_en = '".$_POST['name_en']."' ");

echo json_encode($kashruts['name_he']);