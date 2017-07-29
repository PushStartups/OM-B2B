<?php
require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");



$kashruts_id            =  $_POST['kashruts_id'];




DB::update('kashrut', array(
    "name_en"                  =>  $_POST['name_en'],
    "name_he"                  =>  $_POST['name_he']

),  "id=%d",     $kashruts_id  );


echo json_encode("success");