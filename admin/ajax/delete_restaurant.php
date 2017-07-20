<?php
require_once '../inc/initDb.php';


$restaurant_id      =       $_POST['restaurant_id'];
DB::useDB(B2B_RESTAURANTS);
$menu               =       DB::queryFirstRow("select * from menus where restaurant_id = '$restaurant_id'");
$menu_id            =       $menu['id'];


DB::useDB(B2B_RESTAURANTS);
$categories = DB::query("select * from categories where menu_id = '$menu_id'");
foreach($categories as $category)
{
    DB::useDB(B2B_RESTAURANTS);
    $items = DB::query("select * from items where category_id = '".$category['id']."'");

    foreach ($items as $item)
    {
        DB::useDB(B2B_RESTAURANTS);
        $extras = DB::query("select * from extras where item_id = '" . $item['id'] . "'");

        // DELETE EXTRAS AND SUBITEM
        foreach ($extras as $extra)
        {
            DB::useDB(B2B_RESTAURANTS);
            DB::delete('subitems', "extra_id=%d", $extra['id']);
            DB::useDB(B2B_RESTAURANTS);
            DB::delete('extras', "id=%d", $extra['id']);
        }

        // DELETE ITEMS
        DB::useDB(B2B_RESTAURANTS);
        DB::delete('items', "id=%d", $item['id']);

    }
    // DELETE CATEGORIES
    DB::useDB(B2B_RESTAURANTS);
    DB::delete('categories', "id=%d", $category['id']);
}


// DELETE MENUS
DB::useDB(B2B_RESTAURANTS);
DB::delete('menus', "id=%d", $menu_id);
DB::useDB(B2B_RESTAURANTS);
DB::delete('weekly_availibility', "restaurant_id=%d", $restaurant_id );

DB::useDB(B2B_DB);
DB::delete('user_votes', "restaurant_id=%d", $restaurant_id );

DB::useDB(B2B_RESTAURANTS);
DB::delete('restaurant_tags', "restaurant_id=%d", $restaurant_id );
DB::useDB(B2B_RESTAURANTS);
DB::delete('restaurant_gallery', "restaurant_id=%d", $restaurant_id );

DB::useDB(B2B_DB);
$orders  = DB::query("select * from b2b_orders where restaurant_id = '$restaurant_id'");

foreach($orders as $order)
{
    DB::useDB(B2B_DB);
    DB::delete('b2b_order_detail', "order_id=%d", $order['id']);
    DB::delete('refund', "order_id=%d", $order['id']);
}
DB::useDB(B2B_DB);
DB::delete('b2b_orders', "restaurant_id=%d", $restaurant_id );

DB::useDB(B2B_RESTAURANTS);
DB::delete('delivery_fee', "restaurant_id=%d", $restaurant_id );
DB::useDB(B2B_DB);
DB::delete('company_voting', "restaurant_id=%d", $restaurant_id );
DB::useDB(B2B_DB);
DB::delete('company_rest', "rest_id=%d", $restaurant_id );
DB::useDB(B2B_DB);
DB::delete('b2b_rest_discounts', "rest_id=%d", $restaurant_id );

DB::useDB(B2B_RESTAURANTS);
DB::delete('restaurants', "id=%d", $restaurant_id );
echo json_encode("success");