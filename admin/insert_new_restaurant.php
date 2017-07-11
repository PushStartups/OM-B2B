<?php
require_once 'inc/initDb.php';
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



//$name = $_FILES['logo']['name'];
//$tmp1  = $_FILES['logo']['tmp_name'];
//
//$temp_name1 = $tmp1;
//
//if(move_uploaded_file($temp_name1, "m/logo.png")){
//  //  echo "uploaded";
//    $image_url = "m/".strtolower($_POST['name_en'])."_logo.png";
//}
//else
//{
//    $image_url = "/m/en/img/cs-logo.png";
//}

//
$name = $_FILES['logo']['name'];
$tmp1  = $_FILES['logo']['tmp_name'];

$temp = explode(".", $name);

$logo = "ahmad".round(microtime(true)).rand() . '.' . end($temp);
$temp_name1 = $tmp1;

if(move_uploaded_file($temp_name1, "../m/en/img/".strtolower($_POST['name_en'])."_logo.png")){
   // echo "uploaded";
    $image_url = "m/en/img/".strtolower($_POST['name_en'])."_logo.png";
}
else
{
    $image_url = "/m/en/img/cs-logo.png";
}

//$image_url = "/m/en/img/cs-logo.png";
//DB::insert('restaurants', array(
//
//    "id"                    =>      $id,
//    "name_en"               =>      $_POST['name_en'],
//    "name_he"               =>      $_POST['name_he'],
//    "contact"               =>      $_POST['contact'],
//    "coming_soon"           =>      $_POST['coming_soon'],
//    "hide"                  =>      $_POST['hide'],
//    //"logo"                  =>      "m/en/img/".strtolower($_POST['name_en'])."_logo.png",
//    "logo"                  =>      $image_url,
//    "description_en"        =>      $_POST['description_en'],
//    "description_he"        =>      $_POST['description_he'],
//    "address_en"            =>      $_POST['address_en'],
//    "address_he"            =>      $_POST['address_he'],
//    "city_id"               =>      $_POST['city_id'],
//    "hechsher_en"           =>      $_POST['hechsher_en'],
//    "hechsher_he"           =>      $_POST['hechsher_he'],
//    "pickup_hide"           =>      $_POST['pickup_hide'],
//    "min_amount"           =>       $_POST['min_amount'],
//
//));
//
//DB::insert('menus', array(
//
//    "id"                  =>      $menuId,
//    "restaurant_id"       =>      $id,
//    "name_en"             =>      "Lunch",
//    "name_he"             =>      "ארוחת צהריים",
//    "sort"                =>      $menuSort,
//
//));

echo json_encode("success");