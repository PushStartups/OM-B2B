<?php
require_once '../inc/initDb.php';
DB::query("set names utf8");

DB::insert('cities', array(
    "name_en"              =>  $_POST['name_en'],
    "name_he"              =>  $_POST['name_he'],
));

echo json_encode("success");