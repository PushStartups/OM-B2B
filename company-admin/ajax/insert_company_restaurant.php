<?php
session_start();
require_once '../inc/initDb.php';
$company_id = $_POST['company_id'];
$url = $_POST['url'];
DB::useDB('orderapp_b2b_wui');
foreach ($_POST['rest_name'] as $id)
{
    DB::useDB('orderapp_b2b_wui');

    DB::insert('company_rest', array(
        "company_id" =>  $_SESSION['add_company_id'],
        "rest_id" => $id,
    ));
}
header("location: $url");
