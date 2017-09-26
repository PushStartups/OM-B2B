<?php
require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");



DB::update('extras', array(
    "name_en"               =>      $_POST['name_en'],
    "name_he"               =>      $_POST['name_he'],
    "type"                  =>      $_POST['type'],
    "price_replace"         =>      $_POST['price_replace'],
    "limit"                 =>      $_POST['limit']

), "id=%d", $_POST['extra_id']
);



echo json_encode("success");