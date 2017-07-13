<?php
require_once '../inc/initDb.php';

session_start();
DB::query("set names utf8");



DB::useDB('orderapp_b2b_wui');



DB::update('b2b_users', array(
    "smooch_id"         => $_POST['smooch_id'],
    "name"              => $_POST['name'],
    "contact"           => $_POST['contact'],
    "address"           => $_POST['address'],

), "id=%d", $_POST['users_id']
);


echo json_encode("success");

