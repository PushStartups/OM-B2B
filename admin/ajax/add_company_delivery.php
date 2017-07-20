<?php
require_once '../inc/initDb.php';
DB::query("set names utf8");


DB::useDB(B2B_DB);

$time = $_POST['start_time']."-".$_POST['end_time'];


DB::insert('delivery_timings', array(
    "company_id"              =>  $_POST['company_id'],
    "delivery_timing"              =>  $time

));

echo json_encode("success");