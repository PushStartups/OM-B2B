<?php
require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");



$rest_id            =  $_POST['rest_id'];


DB::useDB('orderapp_b2b');

DB::update('b2b_rest_discounts', array(

    "discount_percent"                  =>  $_POST['discount_percent'],
    "in_time_discount"                  =>  $_POST['in_time_discount']

),  "id=%d",     $rest_id  );


echo json_encode("success");