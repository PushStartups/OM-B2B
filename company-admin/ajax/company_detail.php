<?php
require_once '../inc/initDb.php';
require_once '../inc/functions.php';
DB::query("set names utf8");

$company_id = $_POST['company_id'];

DB::useDB('orderapp_b2b_wui');


//UPDATE COMPANY
DB::update('company', array(

    "registered_company_number"              =>  $_POST['registered_company_number'],

    "contact_name"     =>  $_POST['contact_name'],
    "contact_number"     =>  $_POST['contact_number'],
    "contact_email"     =>  $_POST['contact_email'],

    "email"             =>  $_POST['email'],
    "password"          =>  $_POST['password'],

),  "id=%d",    $company_id   );

$close = $_POST['company_deadline_time'];
$week_en = $_POST['week_en'];

DB::query("UPDATE company_timing set closing_time = '$close' , closing_time_he = '$close' where company_id = '$company_id' and week_en = '$week_en' ");

echo json_encode($_POST['discount_type']);