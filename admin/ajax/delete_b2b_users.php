<?php


require_once '../inc/initDb.php';
$id = $_POST['user_id'];


DB::useDB(B2B_DB);

DB::delete('b2b_orders', "user_id=%d", $id);
DB::delete('b2b_users', "id=%d", $id);

echo json_encode("success");