<?php
session_start();
require_once '../inc/initDb.php';
require_once '../inc/functions.php';

DB::query("set names utf8");

DB::useDB(B2B_RESTAURANTS);
$restaurant = DB::queryFirstRow("select name_en from restaurants where id = '".$_POST['restaurant_id']."'");



DB::useDB(B2B_B2C_COMMON);

DB::update('b2b_ledger', array(
    "restaurant_id"                     =>      $_POST['restaurant_id'],
    "payment_method"                    =>      $_POST['payment_method'],
    "delivery_or_pickup"                =>      $_POST['delivery_or_pickup'],
    "order_no"                          =>      $_POST['order_no'],
    "restaurant_total"                  =>      $_POST['restaurant_total'],
    "customer_grand_total"              =>      $_POST['customer_grand_total'],
    "customer_total_paid_to_restaurant" =>      $_POST['customer_total_paid_to_restaurant'],
    "restaurant_name"                   =>      $restaurant['name_en']

), "id=%d", $_POST['id']
);


echo json_encode("success");