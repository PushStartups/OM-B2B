<?php
require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");



$id            =  $_POST['id'];


DB::useDB('orderapp_b2b');

DB::update('b2b_users', array(
    "name"                  =>  $_POST['name'],
    "smooch_id"                  =>  $_POST['smooch_id'],
    "contact"                  =>  $_POST['contact'],
    "address"                  =>  $_POST['address']

),  "id=%d",     $id  );


echo json_encode("success");