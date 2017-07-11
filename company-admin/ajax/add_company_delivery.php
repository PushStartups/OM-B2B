<?php
session_start();
require_once '../inc/initDb.php';
DB::query("set names utf8");


DB::useDB('orderapp_b2b');

$time = $_POST['start_time']."-".$_POST['end_time'];


DB::insert('delivery_timings', array(
    "company_id"              =>  $_SESSION['company_id'],
    "delivery_timing"         =>  $time

));

echo json_encode("success");