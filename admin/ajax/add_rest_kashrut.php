<?php

require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");


$restaurant_id = $_POST['restaurant_id'];
$url = $_POST['url'];
DB::useDB(B2B_DB);
foreach ($_POST['kash_name'] as $id)
{
    DB::useDB(B2B_RESTAURANTS);

    DB::insert('restaurant_kashrut', array(
        "kashrut_id" =>  $id,
        "restaurant_id" => $_SESSION['kashrut_rest_id'],
    ));
}
header("location: $url");