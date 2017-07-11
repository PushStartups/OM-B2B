<?php


require_once '../inc/initDb.php';
$id = $_POST['extra_id'];

DB::delete('subitems', "extra_id=%d", $id);
DB::delete('extras', "id=%d", $id);

echo json_encode("success");