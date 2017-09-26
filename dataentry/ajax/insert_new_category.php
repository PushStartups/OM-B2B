<?php
require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");

$secondlastRestaurant   =   DB::queryFirstRow("SELECT * FROM categories ORDER BY id DESC  LIMIT 1 , 1");
$secondlastId           =   $secondlastRestaurant['id'];
$secondlastSortId       =   $secondlastRestaurant['sort'];
$id                     =   $secondlastId + 1;
$sort                   =   $secondlastSortId + 1;



$menu_id = DB::queryFirstRow("select restaurant_id from menus where id = '".$_POST['menu_id']."'");

$restaurant = DB::queryFirstRow("select name_en from restaurants where id = '".$menu_id['restaurant_id']."'");
$baseFromJavascript   = $_POST['logo'];

if($_POST['hidden_image_cat'] == '1') {

    $data = $_FILES['logo1']['name'];
    $connect = ftp_connect("35.158.184.248", 21);
    if ($connect) {
        echo $connect;
    } else {
        echo "fasle";
    }
    $result = ftp_login($connect, "dataentry", "orderapp");
    if (!$result)
    {
        echo 'Could not connect to Server';
    } else
    {
        echo "connected";
    }


    ftp_pasv($connect, true);

    $dir = "/img/categories/".$restaurant['name_en'];


    if (!ftp_chdir($connect, $dir))
    {
        ftp_mkdir($connect, $dir);
    }



    $folder = "/img/categories/".$restaurant['name_en']."/";
    @ftp_site($connect, "CHMOD 0777 $folder");


    $destination_file = $folder . $data;

    $source_file = $_FILES['logo1']['tmp_name'];


    $result = ftp_put($connect, $destination_file, $source_file, FTP_BINARY);


    $imagePath = "http://resources.orderapp.com/categories/".$restaurant['name_en']."/".$_FILES['logo1']['name'];

}
else{
    $imagePath = "http://resources.orderapp.com/cs-logo.png";
}


DB::insert('categories', array(

    "menu_id"               =>      $_POST['menu_id'],
    "is_discount"           =>      0,
    "name_en"               =>      $_POST['name_en'],
    "name_he"               =>      $_POST['name_he'],
    "business_offer"        =>      $_POST['business_offer'],
    "image_url"             =>      $imagePath,
    "sort"                  =>      $sort

));

$lastInsertId = DB::insertId();

//"/m/en/img/categories/".$restaurant['name_en']."/".$_POST['name_en'].".png"

echo "success";