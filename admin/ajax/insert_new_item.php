<?php
require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");

$secondlastRestaurant   =   DB::queryFirstRow("SELECT * FROM items ORDER BY id DESC  LIMIT 1 , 1");
$secondlastId           =   $secondlastRestaurant['id'];
$secondlastSortId       =   $secondlastRestaurant['sort'];
//$id                     =   $secondlastId + 1;
$sort                   =   $secondlastSortId + 2;




DB::insert('items', array(
    "category_id"           =>      $_POST['category_id'],
    "hide"                  =>      $_POST['hide'],
    "name_en"               =>      $_POST['name_en'],
    "name_he"               =>      $_POST['name_he'],
    "desc_en"               =>      $_POST['desc_en'],
    "desc_he"               =>      $_POST['desc_he'],
    "price"                 =>      $_POST['price'],
    "sort"                  =>      $sort

));


echo json_encode("success");