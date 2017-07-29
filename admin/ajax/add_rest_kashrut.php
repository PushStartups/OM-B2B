<?php

require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");

$kashruts   =   DB::queryFirstRow("SELECT * FROM kashrut where name_en = '".$_POST['name_en']."' ");
if(DB::count() == 0)
{
    // TAGS DOES NOT EXIST
    DB::insert('kashrut', array(
        "name_en"               =>      $_POST['name_en'],
        "name_he"               =>      $_POST['name_he'],

    ));

    $kashrut_id = DB::insertId();

    DB::insert('restaurant_kashrut', array(
        "restaurant_id"               =>      $_POST['restaurant_id'],
        "kashrut_id"                      =>      $kashrut_id,

    ));

}
else
{
    // TAGS EXIST
    DB::insert('restaurant_kashrut', array(
        "restaurant_id"               =>      $_POST['restaurant_id'],
        "kashrut_id"                      =>      $kashruts['id'],

    ));
}




echo json_encode("success");