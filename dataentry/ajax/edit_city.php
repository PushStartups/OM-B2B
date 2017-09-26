<?php
require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");



$city_id            =  $_POST['city_id'];




DB::update('cities', array(
    "name_en"                  =>  $_POST['name_en'],
    "name_he"                  =>  $_POST['name_he']

),  "id=%d",     $city_id  );


echo json_encode("success");