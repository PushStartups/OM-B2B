<?php

require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");

$tags   =   DB::queryFirstRow("SELECT * FROM tags where name_en = '".$_POST['name_en']."' ");
if(DB::count() == 0)
{
    // TAGS DOES NOT EXIST
    DB::insert('tags', array(
        "name_en"               =>      $_POST['name_en'],
        "name_he"               =>      $_POST['name_he'],

    ));

    $tag_id = DB::insertId();

    DB::insert('restaurant_tags', array(
        "restaurant_id"               =>      $_POST['restaurant_id'],
        "tag_id"                      =>      $tag_id,

    ));

}
else
{
    // TAGS EXIST
    DB::insert('restaurant_tags', array(
        "restaurant_id"               =>      $_POST['restaurant_id'],
        "tag_id"                      =>      $tags['id'],

    ));
}




echo json_encode("success");