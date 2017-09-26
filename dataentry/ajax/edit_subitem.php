<?php
require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");


DB::update('subitems', array(
    "name_en"               =>      $_POST['name_en'],
    "name_he"               =>      $_POST['name_he'],
    "price"                 =>      $_POST['price'],

), "id=%d", $_POST['subitem_id']
);




echo json_encode("success");