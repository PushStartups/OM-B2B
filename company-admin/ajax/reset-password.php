<?php
require_once '../inc/initDb.php';
session_start();

$password = $_POST['password'];
$user_id  = $_POST['user_id'];

//UPDATE THE DESIRE RESTAURANT SORT
DB::useDB('orderapp_b2b');
DB::update('b2b_users', array(
    "password" =>  $password,
), "id=%d", $user_id
);

echo json_encode("success");