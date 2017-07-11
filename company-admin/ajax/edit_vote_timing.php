<?php
require_once '../inc/initDb.php';
session_start();
DB::query("set names utf8");



$id            =  $_POST['id'];



DB::useDB('orderapp_b2b');


DB::update('vote_timings', array(
    "voting_start"                  =>  $_POST['start_time'],
    "voting_end"                  =>  $_POST['end_time']

),  "id=%d",     $id  );


echo json_encode("success");