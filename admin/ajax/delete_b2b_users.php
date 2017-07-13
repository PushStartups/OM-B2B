<?php


require_once '../inc/initDb.php';
$id = $_POST['user_id'];


DB::useDB('orderapp_b2b_wui');

DB::delete('b2b_orders', "user_id=%d", $id);
DB::delete('b2b_users', "id=%d", $id);

echo json_encode("success");