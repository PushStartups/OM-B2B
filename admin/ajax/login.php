<?php
require_once '../inc/initDb.php';
session_start();
$email = $_POST['email'];
$password = $_POST['password'];
DB::useDB(B2B_RESTAURANTS);
$admin = DB::queryFirstRow("select * from admin where id = '1'");


if ($email == $admin['email'] and $password == $admin['password']) {
    //startAdminSession();
    $_SESSION['b2b_admin'] = $email;

    echo json_encode("true");
}
else
{
    echo json_encode("false");
}
