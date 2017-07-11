<?php
require_once '../inc/initDb.php';

$company_id          =   $_POST['company_id'];
$restaurant_name     =   $_POST['rest_name'];

$rest = DB::queryFirstRow("select id from restaurants where name_en = '$restaurant_name'");
$restaurant_id   = $rest['id'];


DB::useDB('orderapp_b2b_wui');
DB::queryFirstRow("select * from company_rest where company_id = '$company_id ' and rest_id = '$restaurant_id' ");
if(DB::count() == 0) {


    DB::insert('company_rest', array(
        "company_id" => $company_id,
        "rest_id" => $restaurant_id,
    ));


    
    echo $output;
}
else{
    echo "already";
}


