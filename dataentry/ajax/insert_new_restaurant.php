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

$max = DB::queryFirstRow("SELECT MAX( sort ) as sort  FROM restaurants");
$getMax = $max['sort'] + 1;



if($_POST['hidden_image'] == '1') {

    $data = $_FILES['logo']['name'];

    $connect = ftp_connect("35.158.184.248", 21);
    if ($connect) {
        echo $connect;
    } else {
        echo "fasle";
    }
    $result = ftp_login($connect, "dataentry", "orderapp");
    if (!$result) {
        echo 'Could not connect to Server';
    } else {
        echo "connected";
    }


    ftp_pasv($connect, true);


    $folder = "/img/";
    @ftp_site($connect, "CHMOD 0777 $folder");


//    $fp = fopen('php://temp', 'r+');
//    fwrite($fp, $data);
//    rewind($fp);
//    ftp_fput($ftp_conn, $remote_file_name, $fp, FTP_ASCII);



    $destination_file = $folder . $data;

    $source_file = $_FILES['logo']['tmp_name'];


    $result = ftp_put($connect, $destination_file, $source_file, FTP_BINARY);


    $imagePath = "http://resources.orderapp.com/".$_FILES['logo']['name'];
}
else{
    $imagePath = "http://resources.orderapp.com/cs-logo.png";
}

$data = $_FILES['logo']['name'];
DB::insert('restaurants', array(

    "name_en"               =>      $_POST['name_en'],
    "name_he"               =>      $_POST['name_he'],
    "contact"               =>      $_POST['contact'],
    "coming_soon"           =>      $_POST['coming_soon'],
    "hide"                  =>      1,
    "logo"                  =>      $imagePath,
    "description_en"        =>      $_POST['description_en'],
    "description_he"        =>      $_POST['description_he'],
    "address_en"            =>      $_POST['area_en'],
    "address_he"            =>      $_POST['area_he'],
    "city_id"               =>      $_POST['city'],
    "hechsher_en"           =>      $_POST['hechsher_en'],
    "hechsher_he"           =>      $_POST['hechsher_he'],
    "pickup_hide"           =>      $_POST['pickup_hide'],
    "min_amount"            =>      $_POST['min_amount'],
    "sort"                  =>      $getMax,
    "lat"                  =>       $_POST['lat'],
    "lng"                  =>       $_POST['lng'],

));

$lastInsertID = DB::insertId();

DB::insert('menus', array(

    "restaurant_id"       =>      $lastInsertID,
    "name_en"             =>      "Lunch",
    "name_he"             =>      "ארוחת צהריים",
    "sort"                =>      $menuSort,

));



echo "success";