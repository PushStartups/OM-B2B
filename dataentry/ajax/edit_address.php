<?php
require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");



$address_id            =  $_POST['address_id'];




DB::update('delivery_fee', array(
    "area_en"                  =>  $_POST['area_en'],
    "area_he"                  =>  $_POST['area_he'],
    "fee"                      =>  $_POST['fee']

),  "id=%d",     $address_id  );


echo json_encode("success");