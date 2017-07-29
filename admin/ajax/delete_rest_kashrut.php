<?php
require_once '../inc/initDb.php';

DB::useDB(B2B_RESTAURANTS);

$rest_kashruts_id            =  $_POST['kashruts_id'];

//if want to delete from kashrut table
//$id = DB::query("select * from restaurant_kashrut where id = '$kashruts_id'");
//$id_kash =  $id['kashrut_id'];

DB::query("delete from restaurant_kashrut where  id = '$rest_kashruts_id' ");


//DB::query("delete from kashrut where  id = '$id_kash' ");

echo json_encode("success");