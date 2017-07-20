<?php
session_start();
require_once '../inc/initDb.php';
$company_id = $_POST['company_id'];
$url = $_POST['url'];
DB::useDB(B2B_DB);
foreach ($_POST['rest_name'] as $id)
{
    DB::useDB(B2B_DB);

    DB::insert('company_rest', array(
        "company_id" =>  $_SESSION['add_company_id'],
        "rest_id" => $id,
    ));
}
header("location: $url");
