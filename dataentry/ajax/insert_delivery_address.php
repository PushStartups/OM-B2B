<?php
require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");


    DB::insert('delivery_fee', array(
        "restaurant_id"            =>  $_POST['restaurant_id'],
        "area_en"                  =>  $_POST['area_en'],
        "area_he"                  =>  $_POST['area_he'],
        "fee"                      =>  $_POST['fee']
    ));



echo json_encode("success");