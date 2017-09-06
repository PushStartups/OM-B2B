<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once (dirname(__FILE__) . '/../../inc/initDb.php');
DB::query("set names utf8");


$orderList = getOrders();
//echo '<pre>'; var_dump($orderList); echo '</pre>';

function getOrders() {

    //GET ORDERS FROM B2B
    $b2b_orders = getOrdersFromB2B();


    echo '<pre>'; var_dump($b2b_orders); echo '</pre>';
    //return $result;
}

function getOrdersFromB2B() {

    DB::useDB('orderapp_b2b_wui');
    $result = DB::query("SELECT * FROM `b2b_orders` WHERE date >= now() - INTERVAL 1 DAY AND `order_status` != 'cancelled'");


    $orders_list = array();

    $orders_item = array(
      "id" => '',
      "date" => '',
      "time" => '',
      "restaurnt_id" => '',
      "sum_order" => '',
    );

    foreach($result as $order) {

        $date_array = explode(" ", $order["date"]);

        $rest_order_obj = json_decode($order["rest_order_object"], true);
        //echo '<pre>'; var_dump($order); echo '</pre>';

        $orders_item["id"] = $order["id"];
        $orders_item["date"] = $date_array[0];
        $orders_item["time"] = $date_array[1];
        $orders_item["restaurnt_id"] = $order["restaurant_id"];
        $orders_item["sum_order"] = $rest_order_obj["actual_total"];
        
        array_push($orders_list, $orders_item);

    }

    return $orders_list;
}