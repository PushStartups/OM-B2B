<?php
require_once '../inc/initDb.php';
DB::query("set names utf8");

$restaurant  =  $_POST['restaurant'];
$company  =  $_POST['company'];


$restrnt_id = DB::queryFirstRow("select id from restaurants where name_en = '$restaurant'");
$restaurant_id   = $restrnt_id['id'];


DB::useDB('orderapp_b2b');

        $compny_id = DB::queryFirstRow("select id from company where name = '$company'");
        $company_id   = $compny_id['id'];


DB::useDB('orderapp_b2b');
DB::insert('b2b_rest_discounts', array(
    "discount_percent"              =>  $_POST['discount'],
    "in_time_discount"              =>  $_POST['in_time_discount'],
    "rest_id"               =>  $restaurant_id,
    "company_id"               =>  $company_id

));

echo json_encode("success");