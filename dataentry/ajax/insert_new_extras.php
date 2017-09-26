<?php
require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");

$secondlastRestaurant   =   DB::queryFirstRow("SELECT * FROM extras ORDER BY id DESC  LIMIT 1 , 1");
$secondlastId           =   $secondlastRestaurant['id'];
$secondlastSortId       =   $secondlastRestaurant['sort'];
$id                     =   $secondlastId + 1;
$sort                   =   $secondlastSortId + 1;


DB::insert('extras', array(
    "item_id"               =>      $_POST['item_id'],
    "name_en"               =>      $_POST['name_en'],
    "name_he"               =>      $_POST['name_he'],
    "type"                  =>      $_POST['type'],
    "price_replace"         =>      $_POST['price_replace'],
    "limit"                 =>      $_POST['limit'],
    "sort"                  =>      $sort

));



echo json_encode("success");