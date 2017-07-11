<?php
require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");

$data  =   $_POST['logo'];
$resp = "chaa";
if($data != "") {
    $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));

    $name_logo = preg_replace('/\s*/', '', $_POST['name_en']);

    $name_logo = strtolower($name_logo);

    if (!is_dir("../../m/en/img")) {
        mkdir("../../m/en/img", 0777);

    }
    $filepath = '../../m/en/img/' . $name_logo . "_logo.png"; // or image.jpg


    $image_url = "";
    if (file_put_contents($filepath, $data)) {
        $image_url = "/m/en/img/" . $name_logo . "_logo.png";
        $resp = "workingg";

    } else {

        $resp = "not working";
        $image_url = "/m/en/img/cs-logo.png";

    }
}



DB::update('restaurants', array(
    "name_en"               =>      $_POST['name_en'],
    "name_he"               =>      $_POST['name_he'],
    "contact"               =>      $_POST['contact'],
    "coming_soon"           =>      $_POST['coming_soon'],
    "hide"                  =>      $_POST['hide'],
    "description_en"        =>      $_POST['description_en'],
    "description_he"        =>      $_POST['description_he'],
    "address_en"            =>      $_POST['address_en'],
    "address_he"            =>      $_POST['address_he'],
    "city_id"               =>      $_POST['city_id'],
    "hechsher_en"           =>      $_POST['hechsher_en'],
    "hechsher_he"           =>      $_POST['hechsher_he'],
    "pickup_hide"           =>      $_POST['pickup_hide'],
    "min_amount"           =>       $_POST['min_amount'],

), "id=%d", $_POST['rest_id']
);


//if($baseFromJavascript != "")
//{
//    DB::update('restaurants', array(
//        "logo"               =>      $_POST['logo'],
//
//    ), "id=%d", $_POST['rest_id']
//    );
//
//}



echo json_encode($resp);