<?php
require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");



$tags_id            =  $_POST['tags_id'];




DB::update('tags', array(
    "name_en"                  =>  $_POST['name_en'],
    "name_he"                  =>  $_POST['name_he']

),  "id=%d",     $tags_id  );


echo json_encode("success");