<?php
require_once '../inc/initDb.php';


$id            =  $_POST['id'];

DB::useDB('orderapp_b2b');
$orders  = DB::query("select * from b2b_orders where user_id = '$id'");

foreach($orders as $order)
{
    DB::useDB('orderapp_b2b');
    DB::delete('b2b_order_detail', "order_id=%d", $order['id']);

}


DB::useDB('orderapp_b2b');
DB::query("delete from b2b_orders where user_id = '$id' ");

DB::useDB('orderapp_b2b');
DB::query("delete from b2b_users where id = '$id' ");



echo json_encode("success");