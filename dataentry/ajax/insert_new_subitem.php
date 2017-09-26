<?php
require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");
$secondlastRestaurant   =   DB::queryFirstRow("SELECT * FROM subitems ORDER BY id DESC  LIMIT 1 , 1");
$secondlastId           =   $secondlastRestaurant['id'];
$secondlastSortId       =   $secondlastRestaurant['sort'];
//$id                     =   $secondlastId + 1;
$sort                   =   $secondlastSortId + 2;

DB::insert('subitems', array(

    "extra_id"              =>      $_POST['extra_id'],
    "name_en"               =>      $_POST['name_en'],
    "name_he"               =>      $_POST['name_he'],
    "price"                 =>      $_POST['price'],
    "sort"                  =>      $sort

));



echo json_encode("success");