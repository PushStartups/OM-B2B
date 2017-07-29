<?php
require_once '../inc/initDb.php';


$kashruts_id            =  $_POST['kashruts_id'];

DB::query("delete from restaurant_kashrut where  kashrut_id = '$kashruts_id' ");


DB::query("delete from kashrut where  id = '$kashruts_id' ");

echo json_encode("success");