<?php
require_once '../inc/initDb.php';
$item_id = $_POST['item_id'];

//GET EXTRAS
$extras = DB::query("select * from extras where item_id = '$item_id'");
foreach($extras as $extra)
{
    DB::delete('subitems', "extra_id=%d", $extra['id']);
    DB::delete('extras', "id=%d", $extra['id']);
}

DB::delete('items', "id=%d", $item_id);


echo json_encode("success");