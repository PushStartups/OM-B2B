<?php
require_once '../inc/initDb.php';

DB::queryFirstRow("select * from business_lunch_detail where category_id = '".$_POST['category_id']."' and item_id = '".$_POST['item_id']."' ");
if(DB::count() == 0)
{
    DB::insert('business_lunch_detail', array(
        "category_id"              =>      $_POST['category_id'],
        "item_id"                  =>      $_POST['item_id'],
        "week_day"                 =>      $_POST['day'],
        "week_cycle"               =>      $_POST['week_cycle'],

    ));
}

DB::update('categories', array(
    "business_offer"        =>      1,

), "id=%d", $_POST['category_id']
);

echo json_encode("success");