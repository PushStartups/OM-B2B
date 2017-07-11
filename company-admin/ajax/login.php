<?php
require_once '../inc/initDb.php';
session_start();
$email = $_POST['email'];
$password = $_POST['password'];
DB::useDB('orderapp_b2b');
$admin = DB::queryFirstRow("select * from company where email = '$email' and password = '$password'");
if(DB::count() > 0)
{
    //startAdminSession();
    $_SESSION['company_admin']          = $email;
    $_SESSION['company_id']             = $admin['id'];
    $_SESSION['company_name']           = $admin['name'];
    $_SESSION['company_discount']       = $admin['discount'];
    echo json_encode($admin['id']);
}
else
{
    echo json_encode("false");
}


