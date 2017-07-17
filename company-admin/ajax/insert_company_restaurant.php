<?php
require_once '../inc/initDb.php';
$company_id = $_POST['company_id'];
$url = $_POST['url'];
DB::useDB('orderapp_restaurants_b2b_wui');
$r = DB::queryFirstRow("select * from restaurants where name_en = '".$_POST['restaurant_name']."'");
$rest_id        =  $r['id'];
$company_id     =  $_POST['company_id'];

DB::useDB('orderapp_b2b_wui');


DB::insert('company_rest', array(
    "company_id" => $company_id,
    "rest_id" => $rest_id ,
));

header("location: $url");
