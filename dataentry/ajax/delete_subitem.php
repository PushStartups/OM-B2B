<?php
require_once '../inc/initDb.php';
$id = $_POST['subitem_id'];

DB::delete('subitems', "id=%d", $id);

echo json_encode("success");