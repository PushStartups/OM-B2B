<?php
require_once '../inc/initDb.php';


$id            =  $_POST['id'];



DB::useDB('orderapp_b2b');

DB::query("delete from vote_timings where id = '$id' ");



echo json_encode("success");