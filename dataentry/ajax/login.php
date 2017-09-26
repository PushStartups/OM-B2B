<?php
require_once '../inc/initDb.php';
session_start();
$email = $_POST['email'];
$password = $_POST['password'];

DB::useDB('orderapp_restaurants_b2b_wui');
$admin = DB::queryFirstRow("select * from editor where id = '1'");


if ($email == $admin['email'] and $password == $admin['password']) {
    //startAdminSession();
    $_SESSION['editor'] = $email;

    echo json_encode("true");
}
else
{
    echo json_encode("false");
}
