<?php
require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");


//RESTAURANT TABLE
$secondlastRestaurant = DB::queryFirstRow("SELECT id FROM restaurants ORDER BY id DESC  LIMIT 1 , 1");
$secondlastId = $secondlastRestaurant['id'];

$id = $secondlastId + 1;


//MENU TABLE
$secondlastMenu = DB::queryFirstRow("SELECT * FROM menus ORDER BY id DESC  LIMIT 1 , 1");
$secondlastMenuId = $secondlastMenu['id'];
$secondlastMenuSort = $secondlastMenu['sort'];

$menuId   = $secondlastMenuId + 1;
$menuSort = $secondlastMenuSort + 1;

$baseFromJavascript   = $_POST['logo'];
$name_en   = $_POST['name_en'];


$image_url = "/m/en/img/".strtolower($_POST['name_en'])."_logo.png";

DB::insert('restaurants', array(

    "name_en"               =>      $_POST['name_en'],
    "name_he"               =>      $_POST['name_he'],
    "contact"               =>      $_POST['contact'],
    "coming_soon"           =>      $_POST['coming_soon'],
    "hide"                  =>      $_POST['hide'],
    "logo"                  =>      $baseFromJavascript,
    "description_en"        =>      $_POST['description_en'],
    "description_he"        =>      $_POST['description_he'],
    "address_en"            =>      $_POST['address_en'],
    "address_he"            =>      $_POST['address_he'],
    "city_id"               =>      $_POST['city_id'],
    "hechsher_en"           =>      $_POST['hechsher_en'],
    "hechsher_he"           =>      $_POST['hechsher_he'],
    "pickup_hide"           =>      $_POST['pickup_hide'],
    "min_amount"           =>       $_POST['min_amount'],

));

$lastInsertID = DB::insertId();

DB::insert('menus', array(

    "restaurant_id"       =>      $lastInsertID,
    "name_en"             =>      "Lunch",
    "name_he"             =>      "ארוחת צהריים",
    "sort"                =>      $menuSort,

));



echo json_encode($lastInsertID);