<?php

//CHECK WHETHER ADMIN LOGGED IN OR NOT
function checkAdminSession() {

    if(empty($_SESSION['company_admin']))
    {
        header("location:login.php");
    }

}

function getSpecificUser($user_id)
{

    DB::useDB('orderapp_b2b_wui');
    return $user = DB::queryFirstRow("select * from b2b_users  where id = '$user_id' ");
}



//GET ALL RESTAURANTS FROM DATABSE (SHOWING ON INDEX.PHP)
function getAllRestaurants()
{
    $restaurants = DB::query("select r.*,cities.name_en as city_name from restaurants as r inner join cities on r.city_id = cities.id where r.id <> '99999' order by r.sort ASC ");
    return $restaurants;
}




//GET ALL RESTAURANTS ON CITY BASIS
function getAllRestaurantsByCity($city_id)
{
    $restaurants = DB::query("select restaurants.*,cities.name_en as city_name from restaurants inner join cities on restaurants.city_id = cities.id where restaurants.city_id = '$city_id' and restaurants.id <> '99999'");
    return $restaurants;
}


// GET TOTAL COUNT OF RESTAURANTS
function getTotalRestaurants()
{
    DB::query("select * from restaurants where id <> '99999'");
    return $restaurants = DB::count();
}



// GET TOTAL COUNT OF RESTAURANTS ON CITY BASIS
function getRestaurantsCountByCity($city_id)
{
    DB::query("select * from restaurants where city_id = '$city_id' and id <> '99999' ");
    return $restaurants = DB::count();
}


// GET ALL ORDERS FROM DATABASE (SHOWING ON ORDERS.PHP)
function getAllOrders()
{
    //$orders = DB::query("select o.*, r.name_en as restaurant_name, u.smooch_id as email from user_orders as o inner join restaurants as r on o.restaurant_id = r.id  inner join users as u on o.user_id = u.id order by o.id DESC ");
    $orders = DB::query("select * from user_orders");
    return $orders;
}

// GET ALL ORDERS FROM DATABASE (SHOWING ON ORDERS.PHP)
function getAllB2BOrders()
{
    DB::useDB('orderapp_b2b_wui');
    $orders = DB::query("select o.*, c.name as company_name, u.smooch_id as email from b2b_orders as o inner join company as c on o.company_id = c.id  inner join b2b_users as u on o.user_id = u.id order by o.id DESC ");
    return $orders;
}

// GET ALL ORDERS FROM DATABASE (SHOWING ON ORDERS.PHP)
function getSpecificUserB2BOrders($user_id)
{
    DB::useDB('orderapp_b2b_wui');
    $orders = DB::query("select o.*, c.name as company_name, u.smooch_id as email from b2b_orders as o inner join company as c on o.company_id = c.id  inner join b2b_users as u on o.user_id = u.id where u.id = '$user_id' order by o.id DESC ");
    return $orders;
}

function UserTotalSpenditure($user_id)
{
    DB::useDB('orderapp_b2b_wui');
    $monthly = DB::queryFirstRow("SELECT SUM( actual_total ) AS monthly_total FROM b2b_orders WHERE MONTH(DATE) = MONTH(CURDATE())  AND user_id =  '$user_id'");
    return $monthly['monthly_total'];
}
function getOrderItems($order_id)
{
    DB::useDB('orderapp_user');
    $order_detail = DB::query("select * from order_detail where order_id = '$order_id'");
    return $order_detail;
}

//GET ALL TIMINGS OF RESTAURANTS
function getAllTimings($restaurant_id)
{

    $timings = DB::query("select * from weekly_availibility where restaurant_id = '$restaurant_id'");
    return $timings;
}



function getOrderItemsB2B($order_id)
{

    DB::useDB('orderapp_b2b_wui');
    $order_detail = DB::query("select * from b2b_order_detail where order_id = '$order_id'");
    return $order_detail;
}




//function getRestaurantNameByOrderId($order_id)
//{
//    $orders = DB::queryFirstRow("select o.*, r.name_en as restaurant_name from user_orders as o  inner join restaurants as r on o.restaurant_id = r.id where o.id = '$order_id'");
//    return $orders;
//}



function getCompanyNameByOrderId($order_id)
{
    DB::useDB('orderapp_b2b_wui');
    $orders = DB::queryFirstRow("select o.*, c.name as company_name from b2b_orders as o  inner join company as c on o.company_id = c.id where o.id = '$order_id'");
    return $orders;
}



function getTotalPriceOfSpecificOrder($order_id)
{
    DB::useDB('orderapp_user');
    $total = 0;
    $orders = DB::query("select * from order_detail where order_id = '$order_id'");
    foreach($orders as $order)
    {
        $total = $total + $order['sub_total'];
    }
    return $total;
}

function getPaymentMethod($order_id)
{
    DB::useDB('orderapp_user');
    $payment                =  DB::queryFirstRow("select * from user_orders where id = '$order_id' ");
    $payment_info           =  $payment['payment_method'];
    $total                  =  $payment['total'];
    $transaction_id         =  $payment['transaction_id'];
    $order_date             =  $payment['order_date'];

    return array('payment_info' => $payment_info, 'total' => $total, 'transaction_id' => $transaction_id, 'order_date' => $order_date );

}

function getPaymentMethodB2B($order_id)
{
    DB::useDB('orderapp_b2b_wui');
    $payment       =  DB::queryFirstRow("select * from b2b_orders where id = '$order_id' ");

    $total                   =  $payment['total'];
    $remaining_balance       =  $payment['discount'];
    $transaction_id          =  $payment['transaction_id'];
    $billing_amount          =  $payment['actual_total'];

    return array('total' => $total, 'transaction_id' => $transaction_id, 'remaining_balance' => $remaining_balance ,'billing_amount' => $billing_amount );
}

function getRefundCount($order_id)
{
    DB::useDB('orderapp_user');
    DB::query("select * from refund where order_id = '$order_id'");
    return $refund_count = DB::count();
}


function getRefundCountB2B($order_id)
{
    DB::useDB('orderapp_b2b_wui');
    DB::query("select * from b2b_refund where order_id = '$order_id'");
    return $refund_count = DB::count();
}

function getRefundDetail($order_id)
{
    DB::useDB('orderapp_user');
    $refund_orders = DB::query("select * from refund where order_id = '$order_id'");
    return $refund_orders;
}



function getTotalRefundAmount($order_id)
{
    DB::useDB('orderapp_user');
    $total = 0;
    $orders = DB::query("select * from refund where order_id = '$order_id'");
    foreach($orders as $order)
    {
        $total = $total + $order['amount'];
    }
    return $total;
}

function getTotalRefundAmountB2B($order_id)
{
    DB::useDB('orderapp_b2b_wui');
    $total = 0;
    $orders = DB::query("select * from b2b_refund where order_id = '$order_id'");
    foreach($orders as $order)
    {
        $total = $total + $order['amount'];
    }
    return $total;
}

function getCurrentTime()
{
    date_default_timezone_set("Asia/Jerusalem");
    $currentTime           =    date("H:i:s");
    $currentDay            =    date("Y-m-d");

    return $currentDay." ".$currentTime;
}

function getAllCities()
{
    $cities = DB::query("select * from cities");
    return $cities;
}

//GET ALL B2B COMPANIES
function getAllCompanies()
{
    DB::useDB('orderapp_b2b_wui');
    $cities = DB::query("select * from company");
    return $cities;
}

//function getRestaurantsOfSpecificCompany($company_id)
//{
//    $restaurants = DB::query("select company_rest.*, restaurants.name_en as restaurants_name from company_rest inner join restaurants on company_rest.rest_id = restaurants.id where company_id = '$company_id'");
//    return  $restaurants;
//}

//GET COMPANY NAME
function getCompanyName($company_id)
{
    DB::useDB('orderapp_b2b_wui');
    $company = DB::queryFirstRow("select * from company where id = '$company_id'");
    return  $company['name'];
}

//GET ALL COMPANIES
function getSpecificCompanies($company_id)
{
    DB::useDB('orderapp_b2b_wui');
    $edit_company = DB::queryFirstRow("select * from company where id = '$company_id'");
    return  $edit_company;
}
//GET ALL COMPANIES TIMINGS
function getSpecificCompanyTiming($company_id)
{
    DB::useDB('orderapp_b2b_wui');
    $edit_company_time = DB::query("select * from company_timing where company_id = '$company_id'");
    return  $edit_company_time;
}


//GET USERS OF SPECIFIC COMPANY
function getUsersOfSpecificCompany($companies_id)
{
    DB::useDB('orderapp_b2b_wui');


    $company = DB::query("select * from b2b_users where company_id = '$companies_id'");
    return $company;
}


// GET MENU ID FROM RESTAURANT ID
function getMenuId($restaurant_id)
{
    $menu = DB::queryFirstRow("select id from menus where restaurant_id = '$restaurant_id'");
    return  $menu['id'];
}

function getAllCategories($menu_id)
{
    return $categories = DB::query("select * from categories where menu_id = '$menu_id'");

}

function getItemsFromCategoryId($category_id)
{

    return $items = DB::query("select * from items where category_id = '$category_id'");

}

//GET CATEGORY NAME
function getCategoryName($category_id)
{
    $category = DB::queryFirstRow("select name_en from categories where id = '$category_id'");
    return  $category['name_en'];
}

function getItemName($item_id)
{
    $item = DB::queryFirstRow("select name_en from items where id = '$item_id'");
    return  $item['name_en'];
}

function getExtraName($extra_id)
{
    $extra = DB::queryFirstRow("select name_en from extras where id = '$extra_id'");
    return  $extra['name_en'];
}

function getExtrasFromItemId($item_id)
{
    return $extras = DB::query("select * from extras where item_id = '$item_id'");
}

function getSubItemsFromItemId($extra_id)
{
    return $subItems = DB::query("select * from subitems where extra_id = '$extra_id'");
}

function getRestaurantName($restaurant_id)
{
    $restaurant = DB::queryFirstRow("select name_en from restaurants where id = '$restaurant_id'");
    return  $restaurant['name_en'];
}

function getSubItem($subitem_id)
{
    return $subitem = DB::queryFirstRow("select * from subitems where id = '$subitem_id'");

}

function getExtra($extra_id)
{
    return $extra = DB::queryFirstRow("select * from extras where id = '$extra_id'");
}

function getItem($item_id)
{
    return $item = DB::queryFirstRow("select * from items where id = '$item_id'");
}

function getCategory($category_id)
{
    return $category = DB::queryFirstRow("select * from categories where id = '$category_id'");
}

function getRestaurant($restaurant_id)
{
    return $restaurant = DB::queryFirstRow("select * from restaurants where id = '$restaurant_id'");
}

//
function getTagsOfSpecificRestaurant($restaurant_id)
{
    return $tags = DB::query("select * from tags inner join restaurant_tags on tags.id = restaurant_tags.tag_id where restaurant_tags.restaurant_id = '$restaurant_id' ");
}

function getSpecificTags($tags_id)
{
    return $tags = DB::queryFirstRow("select * from tags  where id = '$tags_id' ");
}


function getSpecificTagsRestaurant($tags_id)
{
    return $tags = DB::query("select * from restaurants inner join restaurant_tags on restaurants.id = restaurant_tags.tag_id where restaurant_tags.restaurant_id = '$tags_id' ");
}

//GET ALL DELIVERY ADDRESS OF RESTAURANTS
function getAllDeliveryAddress($restaurant_id)
{
    $delivery_address = DB::query("select * from delivery_fee where restaurant_id = '$restaurant_id'");
    return $delivery_address;
}
//GET ALL DELIVERY ADDRESS OF RESTAURANTS
function getSpecificDeliveryAddress($delivery_id)
{
    $delivery_address = DB::queryFirstRow("select * from delivery_fee where id = '$delivery_id'");
    return $delivery_address;
}

function getSpecificCity($city_id)
{
    $city = DB::queryFirstRow("select * from cities where id = '$city_id'");
    return $city;
}

function getAllTags()
{
    return $tags= DB::query("select * from tags");
}

function getSpecificb2bRestDisc($rest_id)
{
    DB::useDB('orderapp_b2b_wui');
    return $rest_discounts = DB::queryFirstRow("select * from b2b_rest_discounts where id = '$rest_id'");
}

function getAllB2BRestDiscounts()
{
    $b2bRestDiscounts  =  DB::query("select brd.*,c.name,r.name_en from b2b_rest_discounts as brd inner join restaurants as r on brd.rest_id = r.id  inner join company as c on brd.company_id = c.id");
    return $b2bRestDiscounts;
}