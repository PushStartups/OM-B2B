<?php

//CHECK WHETHER ADMIN LOGGED IN OR NOT
function checkAdminSession() {

    if(empty($_SESSION['admin']))
    {
        header("location:login.php");
    }

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
    $orders = DB::query("select o.*, r.name_en as restaurant_name, u.smooch_id as email from user_orders as o inner join restaurants as r on o.restaurant_id = r.id  inner join users as u on o.user_id = u.id order by o.id DESC ");
    return $orders;
}

// GET ALL ORDERS FROM DATABASE (SHOWING ON ORDERS.PHP)
function getAllB2BOrders()
{
    $orders = DB::query("select o.*, c.name as company_name, u.smooch_id as email from b2b_orders as o inner join company as c on o.company_id = c.id  inner join b2b_users as u on o.user_id = u.id order by o.id DESC ");
    return $orders;
}



function getOrderItems($order_id)
{
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
    $order_detail = DB::query("select * from b2b_order_detail where order_id = '$order_id'");
    return $order_detail;
}




function getRestaurantNameByOrderId($order_id)
{
    $orders = DB::queryFirstRow("select o.*, r.name_en as restaurant_name from user_orders as o  inner join restaurants as r on o.restaurant_id = r.id where o.id = '$order_id'");
    return $orders;
}



function getCompanyNameByOrderId($order_id)
{
    $orders = DB::queryFirstRow("select o.*, c.name as company_name from b2b_orders as o  inner join company as c on o.company_id = c.id where o.id = '$order_id'");
    return $orders;
}



function getTotalPriceOfSpecificOrder($order_id)
{
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
    $payment                =  DB::queryFirstRow("select * from user_orders where id = '$order_id' ");
    $payment_info           =  $payment['payment_method'];
    $total                  =  $payment['total'];
    $transaction_id         =  $payment['transaction_id'];
    $order_date             =  $payment['order_date'];

    return array('payment_info' => $payment_info, 'total' => $total, 'transaction_id' => $transaction_id, 'order_date' => $order_date );

}

function getPaymentMethodB2B($order_id)
{
    $payment       =  DB::queryFirstRow("select * from b2b_orders where id = '$order_id' ");

    $total                   =  $payment['total'];
    $remaining_balance       =  $payment['discount'];
    $transaction_id          =  $payment['transaction_id'];
    $billing_amount          =  $payment['actual_total'];

    return array('total' => $total, 'transaction_id' => $transaction_id, 'remaining_balance' => $remaining_balance ,'billing_amount' => $billing_amount );
}

function getRefundCount($order_id)
{
    DB::query("select * from refund where order_id = '$order_id'");
    return $refund_count = DB::count();
}


function getRefundCountB2B($order_id)
{
    DB::query("select * from b2b_refund where order_id = '$order_id'");
    return $refund_count = DB::count();
}

function getRefundDetail($order_id)
{
    $refund_orders = DB::query("select * from refund where order_id = '$order_id'");
    return $refund_orders;
}



function getTotalRefundAmount($order_id)
{
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
    $cities = DB::query("select * from company");
    return $cities;
}

function getRestaurantsOfSpecificCompany($company_id)
{
    $restaurants = DB::query("select company_rest.*, restaurants.name_en as restaurants_name from company_rest inner join restaurants on company_rest.rest_id = restaurants.id where company_id = '$company_id'");
    return  $restaurants;
}

//GET COMPANY NAME
function getCompanyName($company_id)
{
    $company = DB::queryFirstRow("select * from company where id = '$company_id'");
    return  $company['name'];
}


//GET USERS OF SPECIFIC COMPANY
function getUsersOfSpecificCompany($companies_id){

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