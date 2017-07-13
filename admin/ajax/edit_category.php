<?php
require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");

$data  =   $_POST['image_url'];
$category_id  =   $_POST['category_id'];

$menu_id = DB::queryFirstRow("select * from categories where id = '".$category_id."'");
$menu_id1 = DB::queryFirstRow("select restaurant_id from menus where id = '".$menu_id['menu_id']."'");

$restaurant = DB::queryFirstRow("select * from restaurants where id = '".$menu_id1['restaurant_id']."'");


//$resp = "chaa";
if($data != "") {
    $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));
    $name_logo = preg_replace('/\s*/', '', $restaurant['name_en']);
    $name_logo = strtolower($name_logo);


    $rests['name_en'] = preg_replace('/\s*/', '', $menu_id['name_en']);
    $rests['name_en'] = strtolower($menu_id['name_en']);



    if(!is_dir("../../m/en/img/categories/" . $restaurant['name_en']))
    {
        mkdir("../../m/en/img/categories/" . $restaurant['name_en'], 0777);

    }
    //$filepath = '../../m/en/img/' . $name_logo . "_logo.png"; // or image.jpg

    $filepath = "../../m/en/img/categories/".$restaurant['name_en']."/".$menu_id['name_en'].".png"; // or image.jpg



    $image_url = "";
    if (file_put_contents($filepath, $data)) {

        $image_url = "/m/en/img/categories/".$restaurant['name_en']."/".$menu_id['name_en'].".png";

        $resp = "workingg"." ".$restaurant['name_en']." ".$menu_id['name_en'];

    }
    else
    {

        $resp = "not working";

        $image_url = "/m/en/img/cs-category.png";

    }
    DB::update('categories', array(
        "image_url"        =>      $image_url,
    ), "id=%d", $_POST['category_id']
    );
}


DB::update('categories', array(
    "name_en"               =>      $_POST['name_en'],
    "name_he"               =>      $_POST['name_he'],
    "business_offer"        =>      $_POST['business_offer'],

), "id=%d", $_POST['category_id']
);



echo json_encode($resp);