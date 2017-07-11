<?php
require_once '../inc/initDb.php';

$category_id = $_POST['category_id'];



//$item_id = $_POST['item_id'];

//GET EXTRAS
$items = DB::query("select * from items where category_id = '$category_id'");

foreach($items as $item)
{
    $extras = DB::query("select * from extras where item_id = '".$item['id']."'");
    foreach($extras as $extra)
    {
        DB::delete('subitems', "extra_id=%d", $extra['id']);
        DB::delete('extras', "id=%d", $extra['id']);
    }
    DB::delete('items', "id=%d", $item['id']);

}

DB::delete('categories', "id=%d", $category_id);


echo json_encode("success");