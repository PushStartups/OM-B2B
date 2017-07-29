<?php

require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");

$kashrut   =   DB::queryFirstRow("SELECT * FROM kashrut where name_en = '".$_POST['name_en']."' ");
if(DB::count() == 0)
{
    // TAGS DOES NOT EXIST
    DB::insert('kashrut', array(
        "name_en"               =>      $_POST['name_en'],
        "name_he"               =>      $_POST['name_he'],

    ));
    $result = 1;

}
else
{
    $result = 0;
}




echo json_encode($result);