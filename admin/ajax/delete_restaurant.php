<?php
require_once '../inc/initDb.php';


$restaurant_id      =       $_POST['restaurant_id'];
$menu               =       DB::queryFirstRow("select * from menus where restaurant_id = '$restaurant_id'");
$menu_id            =       $menu['id'];


$categories = DB::query("select * from categories where menu_id = '$menu_id'");
foreach($categories as $category)
{

    $items = DB::query("select * from items where category_id = '".$category['id']."'");

    foreach ($items as $item)
    {
        $extras = DB::query("select * from extras where item_id = '" . $item['id'] . "'");

        // DELETE EXTRAS AND SUBITEM
        foreach ($extras as $extra)
        {
            DB::delete('subitems', "extra_id=%d", $extra['id']);
            DB::delete('extras', "id=%d", $extra['id']);
        }

        // DELETE ITEMS
        DB::delete('items', "id=%d", $item['id']);

    }
    // DELETE CATEGORIES
    DB::delete('categories', "id=%d", $category['id']);
}


// DELETE MENUS
DB::delete('menus', "id=%d", $menu_id);
DB::delete('weekly_availibility', "restaurant_id=%d", $restaurant_id );

DB::useDB('orderapp_b2b_wui');
DB::delete('user_votes', "restaurant_id=%d", $restaurant_id );

DB::useDB('orderapp_restaurants_b2b_wui');
DB::delete('restaurant_tags', "restaurant_id=%d", $restaurant_id );
DB::delete('restaurant_gallery', "restaurant_id=%d", $restaurant_id );

DB::useDB('orderapp_user');
$orders  = DB::query("select * from user_orders where restaurant_id = '$restaurant_id'");

foreach($orders as $order)
{
    DB::useDB('orderapp_user');
    DB::delete('order_detail', "order_id=%d", $order['id']);
    DB::delete('refund', "order_id=%d", $order['id']);
}
DB::useDB('orderapp_user');
DB::delete('user_orders', "restaurant_id=%d", $restaurant_id );

DB::useDB('orderapp_restaurants_b2b_wui');
DB::delete('delivery_fee', "restaurant_id=%d", $restaurant_id );

DB::useDB('orderapp_b2b_wui');
DB::delete('company_voting', "restaurant_id=%d", $restaurant_id );
DB::delete('company_rest', "rest_id=%d", $restaurant_id );
DB::delete('b2b_rest_discounts', "rest_id=%d", $restaurant_id );

DB::useDB('orderapp_restaurants_b2b_wui');
DB::delete('restaurants', "id=%d", $restaurant_id );
echo json_encode("success");