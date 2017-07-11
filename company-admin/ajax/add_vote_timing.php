<?php
session_start();
require_once '../inc/initDb.php';
DB::query("set names utf8");

DB::useDB('orderapp_b2b');
DB::insert('vote_timings', array(
    "company_id"           =>  $_SESSION['company_id'],
    "voting_start"         =>  $_POST['start_time'],
    "voting_end"           =>  $_POST['end_time'],
));

echo json_encode("success");