<?php

require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");


DB::useDB(B2B_RESTAURANTS);

$admin   =   DB::queryFirstRow("SELECT * FROM admin where email = '".$_POST['email']."' ");
if(DB::count() == 0)
{

    DB::useDB(B2B_RESTAURANTS);

    DB::insert('admin', array(
        "email"               =>      $_POST['email'],
        "password"               =>      $_POST['pass'],
        "role"               =>      $_POST['user_role'],

    ));
    $result = 1;

}
else
{
    $result = 0;
}




echo json_encode($result);