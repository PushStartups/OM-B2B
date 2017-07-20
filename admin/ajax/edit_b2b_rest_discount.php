<?php
require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");



$rest_id            =  $_POST['rest_id'];


DB::useDB(B2B_DB);

DB::update('b2b_rest_discounts', array(

    "discount_percent"                  =>  $_POST['discount_percent']

),  "id=%d",     $rest_id  );


echo json_encode("success");