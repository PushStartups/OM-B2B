<?php
require_once '../inc/initDb.php';
session_start();
$email = $_POST['email'];
$password = $_POST['password'];
DB::useDB('orderapp_b2b');
$admin = DB::queryFirstRow("select * from b2b_users where user_name = '$email' and password = '$password'");
if(DB::count() > 0)
{
    //startAdminSession();
    $_SESSION['b2b_user_admin'] = $email;
    $_SESSION['user_id']        = $admin['id'];
    $_SESSION['email']          = $admin['smooch_id'];
    echo json_encode($admin['id']);
}
else
{
    echo json_encode("false");
}


