<?php

require      'vendor/autoload.php';
require      'PHPMailer/PHPMailerAutoload.php';
require_once 'inc/initDb.php';
require_once 'functions.php';


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Mailgun\Mailgun;


DB::query("set names utf8");

define("B2B_DB","orderapp_b2b_wui");
define("B2B_RESTAURANTS","orderapp_restaurants_b2b_wui");

// ORDER RECEIVE EMAILS FOR SERVERS

// DEV SERVER
if($_SERVER['HTTP_HOST'] == "devb2b.orderapp.com")
{
    define("EMAIL","devb2borders@orderapp.com");
    define("B2BLINK","devb2b.orderapp.com");
}


// QA SERVER
else if($_SERVER['HTTP_HOST'] == "qab2b.orderapp.com"){

    define("EMAIL","qab2borders@orderapp.com");
    define("B2BLINK","qab2b.orderapp.com");
}


// PRODUCTION SERVER
else
{
    define("EMAIL","b2borders@orderapp.com");
    define("B2BLINK","b2b.orderapp.com");
}


// SERVER URL TO UPLOAD CONTENT

// SLIM INITIALIZATION
$app = new \Slim\App();



//  USER LOGIN FOR B2B
$app->post('/b2b_user_login', function ($request, $response, $args)
{
    try{

        $user_name = $request->getParam('user_name');
        $password  = $request->getParam('password');

        $obj      = '';

        DB::useDB('orderapp_b2b_wui');


        $userDB = DB::queryFirstRow("select * from b2b_users where user_name = '$user_name' and password = '$password'");


        if (DB::count() > 0)
        {
            $company_id                  =   $userDB['company_id'];
            $companyDB                   =   DB::queryFirstRow("select * from company where id = $company_id");

            $obj['company_name']         =   $companyDB['name'];
            $obj['user_id']              =   $userDB['id'];
            $obj['error']                =   false;


        }
        else
        {
            $user = DB::queryFirstRow("select * from b2b_users where user_name = '$user_name'");

            $obj['error'] = true;

            if (DB::count() == 0) {

                $obj['field'] = "user-name";

            }
            else{

                $obj['field'] = "password";

            }

        }

        // RESPONSE RETURN TO REST API CALL
        $response = $response->withStatus(202);
        $response = $response->withJson($obj);
        return $response;
    }

    catch(MeekroDBException $e) {


        $response =  $response->withStatus(500);
        $response =  $response->withHeader('Content-Type', 'text/html');
        $response =  $response->write( $e->getMessage());
        return $response;

    }


});




//  USER LOGIN FOR B2B FROM SESSION
$app->post('/confirm_user_login', function ($request, $response, $args)
{
    try{

        $user_id = $request->getParam('user_id');

        $obj      = '';
        $user     = '';
        $company  = '';
        $discount = '';


        DB::useDB('orderapp_b2b_wui');


        $userDB = DB::queryFirstRow("select * from b2b_users where id = '$user_id'");


        if (DB::count() > 0)
        {

            $company_id         = $userDB['company_id'];
            $companyDB          = DB::queryFirstRow("select * from company where id = $company_id");


            // CURRENT TIME OF ISRAEL
            date_default_timezone_set("Asia/Jerusalem");

            $today = date("Y-m-d");


            // IF DISCOUNT TYPE IS DAILY

            if($companyDB['discount_type'] == "daily") {

                if ($today > $userDB['date']) {


                    $discount = $companyDB['discount'];
                    DB::query("UPDATE b2b_users SET date = '$today', discount = '$discount'  WHERE id = '".$userDB['id']."'");


                }
                else {

                    $discount = $userDB['discount'];

                }

            }

            // IF DISCOUNT TYPE IS MONTHLY

            else{

                $monthUser = date('m', strtotime($userDB['date']));
                $todayMonth =  date("m");;

                if($todayMonth > $monthUser)
                {
                    $discount = $company['discount'];
                    DB::query("UPDATE b2b_users SET date = '$today', discount = '$discount'  WHERE id = '".$userDB['id']."'");
                }
                else{

                    $discount = $userDB['discount'];

                }

            }

            DB::useDB('orderapp_b2b_wui');

            DB::query("select * from b2b_orders where user_id = '$user_id' AND order_status = 'pending'");


            $user['user_id']                    =   $userDB['id'];
            $user['name']                       =   $userDB['name'];
            $user['email']                      =   $userDB['smooch_id'];
            $user['contact']                    =   $userDB['contact'];
            $user['userDiscountFromCompany']    =   $discount;
            $company['company_id']              =   $company_id;
            $company['company_name']            =   $companyDB['name'];
            $company['company_address']         =   $companyDB['delivery_address'];
            $company['company_discount']        =   $companyDB['discount'];
            $company['discount_type']           =   $companyDB['discount_type'];
            $company['lat']                     =   $companyDB['lat'];
            $company['lng']                     =   $companyDB['lng'];


            $obj['company']                 	=   $company;
            $obj['user']                    	=   $user;
            $obj['error']                   	=   false;
            $obj['on_way_order_count']          =   DB::count();

        }
        else
        {
            $obj['error'] = true;

        }


        // RESPONSE RETURN TO REST API CALL
        $response = $response->withStatus(202);
        $response = $response->withJson($obj);
        return $response;

    }

    catch(MeekroDBException $e) {


        $response =  $response->withStatus(500);
        $response =  $response->withHeader('Content-Type', 'text/html');
        $response =  $response->write( $e->getMessage());
        return $response;

    }

});









//  SEND CREDENTIAL DETAIL BACK TO USER IN CASE OF FORGET PASSWORD
$app->post('/forgot_email', function ($request, $response, $args){

    DB::useDB('orderapp_b2b_wui');

    $msg = '';
    $user_email = $request->getParam('email');


    $userLoginInfo = DB::queryFirstRow("select * from b2b_users WHERE smooch_id = %s",$user_email);


    if(DB::count() > 0)
    {

        $username = $userLoginInfo['user_name'];
        $password = $userLoginInfo['password'];

        $is_error = mailForgotPassword($password, $username, $user_email);

        ob_end_clean();

        $msg['error'] = false;

    }
    else
    {

        $msg['error'] = true;

    }

    $response = $response->withStatus(202);
    $response = $response->withJson($msg);
    return $response;

});





//  GET ALL RESTAURANT AGAINST USER COMPANY
$app->post('/get_all_restaurants', function ($request, $response, $args)
{
    try
    {
        DB::useDB('orderapp_b2b_wui');

        $company_id = $request->getParam('company_id');


        // CHECK COMPANY ORDERING IS OPEN OR CLOSED

        $restaurantTimings = DB::query("select * from company_timing where company_id = '" . $company_id . "'");


        // RESTAURANT AVAILABILITY ACCORDING TO TIME
        $currentCompanyOpenStatus = false;
        $delivery_time = null;
        $delivery_time_str = null;
        $milisec = null; // DELIVERY TIME CLOSED


        // CURRENT TIME OF ISRAEL
        date_default_timezone_set("Asia/Jerusalem");
        $currentTime = date("H:i");
        $dayOfWeek   = date('l');


        $today_timings = "";


        foreach ($restaurantTimings as $singleTime) {


            if ($singleTime['week_en'] == $dayOfWeek) {


                $delivery_time = $singleTime['delivery_timing'];


                if($singleTime['opening_time'] != "Closed") {


                    $today_timings     = $singleTime['opening_time'] . " - " . $singleTime['closing_time'];
                    $delivery_time_str = $singleTime['closing_time']." - ".$singleTime['delivery_timing'];


                    $openingTime = DateTime::createFromFormat('H:i', $singleTime['opening_time']);
                    $closingTime = DateTime::createFromFormat('H:i', $singleTime['closing_time']);


                    $currentTimes =  DateTime::createFromFormat('H:i', date('H:i'));


                    $deliveryTime = strtotime($singleTime['delivery_timing']);
                    $deliveryTime = date('H:i',$deliveryTime);
                    $deliveryTime =  DateTime::createFromFormat('H:i', $deliveryTime);


                    if($deliveryTime > $currentTimes)
                    {

                        $since_start = $currentTimes->diff($deliveryTime);
                        $milisec = 0;

                        $milisec  = $milisec + ($since_start->days * 24 * 60);
                        $milisec  = $milisec + ($since_start->h * 60 * 60);
                        $milisec  = $milisec + ($since_start->i * 60);

                        $milisec = ($milisec  * 1000);

                    }
                    else{

                        $milisec = null;
                    }


                    if ($currentTimes >= $openingTime && $currentTimes <= $closingTime) {

                        $currentCompanyOpenStatus = true;

                    }
                    else {


                        $currentCompanyOpenStatus = false;

                    }

                }
                else
                {
                    $currentCompanyOpenStatus = false;
                }


                break;
            }
        }



        $companyDetail = DB::queryFirstRow("select * from company where id = '$company_id'");


        // GET ALL RESTAURANT ID'S ASSOCIATED WITH COMPANY

        $rest_ids = DB::query("select rest_id from company_rest where company_id = '$company_id'");

        $restaurants = Array();

        $results = Array();



        DB::useDB('orderapp_restaurants_b2b_wui');

        // GET RESTAURANTS DETAIL ON THE BASIS OF ID FOR THIS COMPANY

        $cnt = 0;

        foreach ($rest_ids as $restt_id)
        {

            $rest =  DB::queryFirstRow("select * from restaurants where id = '" . $restt_id['rest_id'] . "'");

            if (DB::count() > 0)
            {
                $results[$cnt] = $rest;
                $cnt++;
            }

        }



        $count = 0;

        foreach ($results as $result) {


            DB::useDB('orderapp_restaurants_b2b_wui');


            // GET TAGS OF RESTAURANT i.e BURGER , PIZZA
            $tagsIds = DB::query("select tag_id from restaurant_tags where restaurant_id = '" . $result['id'] . "'");

            $tags = Array();
            $count2 = 0;

            foreach ($tagsIds as $id) {


                DB::useDB('orderapp_restaurants_b2b_wui');
                $tag = DB::queryFirstRow("select * from tags where id = '" . $id["tag_id"] . "'");
                $tags[$count2] = $tag;
                $count2++;
            };



            // GET KOSHER OF RESTAURANT i.e MEHADRIN
            DB::useDB('orderapp_restaurants_b2b_wui');
            $kashrutIds = DB::query("select kashrut_id from restaurant_kashrut where restaurant_id = '" . $result['id'] . "'");

            $kashrut = Array();
            $count3 = 0;

            foreach ($kashrutIds as $id) {


                DB::useDB('orderapp_restaurants_b2b_wui');
                $kashruts  =  DB::queryFirstRow("select * from kashrut where id = '" . $id["kashrut_id"] . "'");
                $kashrut[$count3]  =  $kashruts;
                $count3++;
            };



            // GET GALLERY OF RESTAURANT
            DB::useDB('orderapp_restaurants_b2b_wui');

            $galleryImages = DB::query("select url from restaurant_gallery where restaurant_id = '" . $result['id'] . "'");



            // RETRIEVING RESTAURANT TIMINGS i.e SUNDAY   STAT_TIME : 12:00  END_TIME 21:00;

            DB::useDB('orderapp_b2b_wui');


            // GET B2B PERCENTAGE DISCOUNT ON THIS ITEM

            $in_time_discount = 0;

            DB::useDB('orderapp_b2b_wui');
            $percentage_discount = DB::queryFirstRow("select * from b2b_rest_discounts where rest_id = '" .  $result['id'] . "' AND company_id = '".$company_id."'");

            if(DB::count() == 0)
            {
                // NO DISCOUNT FOUND
                $percentage_discount = '0';

            }
            else{

                $percentage_discount = $percentage_discount['discount_percent'];

            }


            DB::useDB('orderapp_restaurants_b2b_wui');
            $city = DB::queryFirstRow("select * from cities where id = '" . $result['city_id'] . "'");


            // CREATE DEFAULT RESTAURANT OBJECT;
            $restaurant = [

                "id"                   => $result["id"],                // RESTAURANT ID
                "city_en"              => $city["name_en"],             // CITY NAME EN
                "city_he"              => $city["name_he"],             // CITY NAME HE
                "name_en"              => $result["name_en"],           // RESTAURANT NAME
                "name_he"              => $result["name_he"],           // RESTAURANT NAME
                "contact"              => $result["contact"],           // RESTAURANT CONTACT
                "min_amount"           => $companyDetail['min_order'],  // COMPANY MINIMUM AMOUNT
                "tags"                 => $tags,                        // RESTAURANT TAGS
                "kashrut"              => $kashrut,                     // RESTAURANT KASHRUT
                "logo"                 => $result["logo"],              // RESTAURANT LOGO
                "description_en"       => $result["description_en"],    // RESTAURANT DESCRIPTION
                "description_he"       => $result["description_he"],    // RESTAURANT DESCRIPTION
                "address_en"           => $result["address_en"],        // RESTAURANT ADDRESS
                "address_he"           => $result["address_he"],        // RESTAURANT ADDRESS
                "hechsher_en"          => $result["hechsher_en"],       // RESTAURANT HECHSHER
                "hechsher_he"          => $result["hechsher_he"],       // RESTAURANT HECHSHER
                "gallery"              => $galleryImages,               // RESTAURANT GALLERY
                "rest_lat"             => $result["lat"],               // LATITUDE
                "rest_lng"             => $result["lng"],               // LONGITUDE
                "timings"              => $restaurantTimings,           // RESTAURANT WEEKLY TIMINGS
                "today_timings"        => $today_timings,               // TODAY TIMINGS
                "percentage_discount"  => $percentage_discount,         // B2B PERCENTAGE DISCOUNT
            ];

            $restaurants[$count] = $restaurant;
            $count++;
        }

        $db_restaurant_tags = DB::query("select * from tags");


        $db_restaurant_kashrut = DB::query("select * from kashrut");


        $resp = [

            "restaurants"           => $restaurants,
            "company_open_status"   => $currentCompanyOpenStatus,
            "appox_delivey_time"    => $delivery_time,               // APPOX DELIVERY TIME (1 HOUR AFTER ORDER CLOSED)
            "delivery_time_str"     => $delivery_time_str,           // i.e 10:45 - 11:45
            "db_tags"               => $db_restaurant_tags,          //
            "db_kashrut"            => $db_restaurant_kashrut,
            "delivery_time_milisec" => $milisec

        ];


        // RESPONSE RETURN TO REST API CALL
        $response = $response->withStatus(202);
        $response = $response->withJson($resp);
        return $response;

    }


    catch(MeekroDBException $e) {

        $response =  $response->withStatus(500);
        $response =  $response->withHeader('Content-Type', 'text/html');
        $response =  $response->write( $e->getMessage());
        return $response;
    }

});






//  GET ALL PAST ORDERS FOR LAST WEEKS
$app->post('/get_all_past_orders', function ($request, $response, $args)
{
    try
    {
        DB::useDB('orderapp_b2b_wui');


        $user_id = $request->getParam('user_id');
        $filter = $request->getParam('filter');

        if($filter == "last_week") {


            $results = DB::query(" SELECT * FROM b2b_orders WHERE date > DATE_SUB( NOW( ) , INTERVAL 1 WEEK )  AND user_id = $user_id AND order_status <> 'pending' AND ignore_old_reorder = 'false'  order by id DESC");

        }
        else{

            $start_date = $request->getParam('start_date');
            $end_date = $request->getParam('end_date');

            $start_date = DateTime::createFromFormat('m/d/Y', $start_date);
            $end_date = DateTime::createFromFormat('m/d/Y', $end_date);

            $start_date = $start_date->format('Y-m-d');
            $end_date = $end_date->format('Y-m-d');


            $results = DB::query(" SELECT * FROM b2b_orders WHERE date BETWEEN '$start_date'  AND  '$end_date' AND user_id = $user_id AND order_status <> 'pending' AND ignore_old_reorder = 'false'  order by id DESC");
        }


        $ctn = 0;


        foreach ($results as $result)
        {

            DB::useDB('orderapp_restaurants_b2b_wui');

            $restaurant   =  DB::queryFirstRow("select name_en,logo from restaurants where id = '" . $result['restaurant_id'] . "'");

            $results[$ctn]['rest_name'] = $restaurant['name_en'];
            $results[$ctn]['logo'] = $restaurant['logo'];
            $results[$ctn]['rest_order_object'] = "";

            DB::useDB('orderapp_b2b_wui');

            $order_detail =  DB::query("select * from b2b_order_detail where order_id = '" . $result['id'] . "'");
            $results[$ctn]['order_detail'] = $order_detail;

            $date = explode(" ",$result['date']);
            $date = DateTime::createFromFormat('Y-m-d',$date[0]);
            $date = $date->format('d/m/y');

            $results[$ctn]['date'] = $date;

            $ctn++;

        }


        // RESPONSE RETURN TO REST API CALL
        $response = $response->withStatus(202);
        $response = $response->withJson($results);
        return $response;


    }
    catch(MeekroDBException $e) {


        $response =  $response->withStatus(500);
        $response =  $response->withHeader('Content-Type', 'text/html');
        $response =  $response->write( $e->getMessage());
        return $response;


    }

});





//  GET ALL PAST ORDERS FOR LAST WEEKS
$app->post('/get_all_pending_orders', function ($request, $response, $args)
{
    try
    {

        DB::useDB('orderapp_b2b_wui');


        $user_id = $request->getParam('user_id');


        // CHECK DELIVERY TIME IS PASSED OR NOT

        $today = date('l');
        DB::useDB(B2B_DB);

        $get_company_id    =  DB::queryFirstRow("select company_id from b2b_users where id = '$user_id'");


        DB::useDB(B2B_DB);
        $get_delivery_time =  DB::queryFirstRow("select * from company_timing where week_en = '$today' and  company_id = '".$get_company_id['company_id']."'");


        DB::useDB(B2B_DB);
        $all_user_orders = DB::query("select * from b2b_orders where user_id = '$user_id' AND order_status = 'pending' ");




        foreach($all_user_orders as $orders)
        {

            date_default_timezone_set("Asia/Jerusalem");

            $current_time =  DateTime::createFromFormat('H:i', date('H:i'));
            $current_date =  DateTime::createFromFormat('Y-m-d', date('Y-m-d'));

            $order_date = strtotime($orders['date']);
            $order_date = date('Y-m-d',$order_date);
            $order_date   =  DateTime::createFromFormat('Y-m-d', $order_date);


            $delivery_time = strtotime($get_delivery_time['delivery_timing']);
            $delivery_time = date('H:i',$delivery_time);
            $delivery_time   =  DateTime::createFromFormat('H:i', $delivery_time);
            

            if ( $current_date > $order_date )
            {

                DB::useDB(B2B_DB);
                DB::query("UPDATE b2b_orders SET order_status = 'delivered'  WHERE  id = '".$orders['id']."'");
            }
            else{

                if ( $current_time > $delivery_time )
                {

                    DB::useDB(B2B_DB);
                    DB::query("UPDATE b2b_orders SET order_status = 'delivered'  WHERE  id = '".$orders['id']."'");
                }

            }
        }




        $results  =  DB::query("select * from b2b_orders where user_id = '$user_id' AND order_status = 'pending' order by id DESC ");


        $ctn = 0;


        foreach ($results as $result)
        {

            DB::useDB('orderapp_restaurants_b2b_wui');

            $restaurant   =  DB::queryFirstRow("select * from restaurants where id = '" . $result['restaurant_id'] . "'");

            DB::useDB('orderapp_restaurants_b2b_wui');

            $city = DB::queryFirstRow("select * from cities where id = '" . $restaurant['city_id'] . "'");

            $restaurant['city_name'] = $city['name_en'];

            $results[$ctn]['rest'] = $restaurant;

            $results[$ctn]['rest_order_object'] = "";

            DB::useDB('orderapp_b2b_wui');

            $order_detail =  DB::query("select * from b2b_order_detail where order_id = '" . $result['id'] . "'");
            $results[$ctn]['order_detail'] = $order_detail;

            $ctn++;

        }


        // RESPONSE RETURN TO REST API CALL
        $response = $response->withStatus(202);
        $response = $response->withJson($results);
        return $response;


    }
    catch(MeekroDBException $e) {


        $response =  $response->withStatus(500);
        $response =  $response->withHeader('Content-Type', 'text/html');
        $response =  $response->write( $e->getMessage());
        return $response;


    }

});

//  GET RE-ORDER OBJECT
$app->post('/get_reorder', function ($request, $response, $args)
{
    $order_id = $request->getParam('order_id');

    DB::useDB(B2B_DB);
    $rest_order_object    =  DB::queryFirstRow("select rest_order_object from b2b_orders where id = '$order_id'");

    // RESPONSE RETURN TO REST API CALL
    $response = $response->withStatus(202);
    $response = $response->withJson($rest_order_object['rest_order_object']);
    return $response;

});


// CANCEL ORDER
$app->post('/get_db_tags_and_kashrut', function ($request, $response, $args)
{

    DB::useDB(B2B_RESTAURANTS);

    $company_id = $request->getParam('company_id');
    $user_id = $request->getParam('user_id');


    DB::useDB(B2B_DB);
    $company_restaurants = DB::query("select  rest_id from company_rest where company_id = '$company_id'");

    try{

        DB::useDB(B2B_RESTAURANTS);
        $db_restaurant_tags = DB::query("select * from tags");


        $ctn = 0;
        foreach ($db_restaurant_tags as $tag)
        {
            $count = 0;
            foreach ($company_restaurants as $company_restaurant)
            {
                DB::useDB(B2B_RESTAURANTS);
                $result = DB::queryFirstRow("select COUNT(*) as count from restaurant_tags where restaurant_id = '".$company_restaurant['rest_id']."' AND tag_id = '".$tag['id']."' ");
                $count  = $count + $result['count'];
            }

            $db_restaurant_tags[$ctn]['count'] = $count;
            $ctn++;
        }


        DB::useDB(B2B_RESTAURANTS);
        $db_restaurant_kashrut = DB::query("select * from kashrut");
        $ctn1 = 0;
        foreach ($db_restaurant_kashrut as $kashrut)
        {
            $count1 = 0;
            foreach ($company_restaurants as $company_restaurant)
            {
                DB::useDB(B2B_RESTAURANTS);
                $result1 = DB::queryFirstRow("select COUNT(*) as count from restaurant_kashrut where restaurant_id = '".$company_restaurant['rest_id']."' AND kashrut_id = '".$kashrut['id']."' ");
                $count1  = $count1 + $result1['count'];
            }

            $db_restaurant_kashrut[$ctn1]['count'] = $count1;
            $ctn1++;
        }


        DB::useDB(B2B_DB);
        $userDB = DB::queryFirstRow("select * from b2b_users where id = '$user_id'");

        $resp = [

            "db_tags"                       => $db_restaurant_tags,          //
            "db_kashrut"                    => $db_restaurant_kashrut,

        ];
        // RESPONSE RETURN TO REST API CALL
        $response = $response->withStatus(202);
        $response = $response->withJson($resp);
        return $response;

    }


    catch(MeekroDBException $e) {

        $response =  $response->withStatus(500);
        $response =  $response->withHeader('Content-Type', 'text/html');
        $response =  $response->write( $e->getMessage());
        return $response;
    }


});



//  WEB HOOK GET DATA OF CATEGORIES WITH ITEMS

$app->post('/categories_with_items', function ($request, $response, $args)
{

    try {

        $id = $request->getParam('restaurantId');
        $company_id = $request->getParam('company_id');

        // GET MENUS FOR RESTAURANT i.e LUNCH
        $menu = DB::queryFirstRow("select * from menus where restaurant_id = '" . $id . "'");

        // GET CATEGORIES OF RESTAURANT i.e ANGUS SALAD , ANGUS BURGER
        $categories = DB::query("select * from categories where menu_id = '" . $menu['id'] . "'");

        $count2 = 0;
        foreach ($categories as $category) {

            $items = DB::query("select * from items where category_id = '" . $category["id"] . "'");

            $count3 = 0;
            // CHECK FOR ITEMS PRICE ZERO
            foreach ($items as $item) {
                if ($item['price'] == 0) {
                    $extras = DB::query("select * from extras where item_id = '" . $item["id"] . "' AND type = 'One' AND price_replace=1");
                    // EXTRAS WITH TYPE OME AND PRICE REPLACE 1

                    foreach ($extras as $extra) {
                        $subItems = DB::query("select * from subitems where extra_id = '" . $extra["id"] . "'");
                        $lowestPrice = $subItems[0]['price'];
                        foreach ($subItems as $subitem) {
                            if ($subitem['price'] < $lowestPrice) {
                                $lowestPrice = $subitem['price'];
                            }
                        }

                        $items[$count3]['price'] = $lowestPrice;

                    }
                    //break;
                }
                $count3++;
            }


            $categories[$count2]['items'] = $items;
            $count2++;
        }


        // GET B2B PERCENTAGE DISCOUNT ON THIS ITEM

        DB::useDB('orderapp_b2b_wui');
        $percentage_discount = DB::queryFirstRow("select * from b2b_rest_discounts where rest_id = '" . $id . "' AND company_id = '".$company_id."'");

        if(DB::count() == 0)
        {
            // NO DISCOUNT FOUND
            $percentage_discount = '0';

        }
        else{

            $percentage_discount = $percentage_discount['discount_percent'];

        }

        // CREATE DEFAULT OBJECT FOR ITEMS AND CATEGORIES;
        $data = [

            "menu_name_en" => $menu['name_en'],                        // MENU NAME EN
            "menu_name_he" => $menu['name_he'],                        // MENU NAME HE
            "categories_items" => $categories,                         // CATEGORIES AND ITEMS
            "percentage_discount" => $percentage_discount              // PERCENTAGE DISCOUNT OF RESTAURANT

        ];


        // RESPONSE RETURN TO REST API CALL
        $response = $response->withStatus(202);
        $response = $response->withJson($data);
        return $response;

    }
    catch(MeekroDBException $e) {

        $response =  $response->withStatus(500);
        $response =  $response->withHeader('Content-Type', 'text/html');
        $response =  $response->write( $e->getMessage());
        return $response;
    }

});



// GET EXTRAS WITH SUBITEMS
$app->post('/extras_with_subitems', function ($request, $response, $args) {

    try {

        DB::useDB(B2B_RESTAURANTS);

        //GETTING ITEM_ID FROM CLIENT SIDE
        $item_id = $request->getParam('itemId');

        //GETTING EXTRAS OF ITEMS i,e ADDONS,SAUCES ETC
        $extra = DB::query("select * from extras where item_id = '$item_id'");

        $countExtra = 0;

        foreach ($extra as $extras) {

            //GETTING SUNITEMS OF EXTRAS i,e KETCHUP,AMERICAN FRIES
            $subItems = DB::query("select * from subitems where extra_id = '" . $extras["id"] . "'");

            $extra[$countExtra]['subitems'] = $subItems;

            $countExtra++;

        }
        $data = [
            "extra_with_subitems" => $extra                           // EXTRA AND ITEMS
        ];


        // RESPONSE RETURN TO REST API CALL
        $response = $response->withStatus(202);
        $response = $response->withJson($data);
        return $response;
    }
    catch(MeekroDBException $e) {

        $response =  $response->withStatus(500);
        $response =  $response->withHeader('Content-Type', 'text/html');
        $response =  $response->write( $e->getMessage());
        return $response;
    }

});



// ADD NEW CARD AGAINST USER

$app->post('/store_credit_card_info', function ($request, $response, $args){


    $card_no        = $request->getParam('card_no');
    $expiry         = $request->getParam('expiry');
    $cvv            = $request->getParam('cvv');
    $email          = $request->getParam('user_email');

    $rest = "Error";
    $cgConf['tid']='8804324';
    $cgConf['amount']= 1;
    $cgConf['user']='pushstart';
    $cgConf['password']='OE2@38sz';
    $cgConf['cg_gateway_url']="https://cgpay5.creditguard.co.il/xpo/Relay";

    $poststring = 'user='.$cgConf['user'];
    $poststring .= '&password='.$cgConf['password'];

    /*Build Ashrait XML to post*/
    $poststring.='&int_in=<ashrait>
							<request>
							<language>ENG</language>
							<command>doDeal</command>
							<requestId/>
							<version>1000</version>
							<doDeal>
								<terminalNumber>'.$cgConf['tid'].'</terminalNumber>
								<authNumber/>
								<transactionCode>Phone</transactionCode>
								<transactionType>Debit</transactionType>
								<total>'.$cgConf['amount'].'</total>
								<creditType>RegularCredit</creditType>
								<cardNo>'.$card_no.'</cardNo>
								<cvv>'.$cvv.'</cvv>
								<cardExpiration>'.$expiry.'</cardExpiration>
								<validation>AutoComm</validation>
								<numberOfPayments/>
								<customerData>
									<userData1>'.$email.'</userData1>
									<userData2/>
									<userData3/>
									<userData4/>
									<userData5/>
								</customerData>
								<currency>ILS</currency>
								<firstPayment/>
								<periodicalPayment/>
								<user>Push</user>	
										
							</doDeal>
						</request>
					</ashrait>';


    //init curl connection
    if( function_exists( "curl_init" )) {
        $CR = curl_init();
        curl_setopt($CR, CURLOPT_URL, $cgConf['cg_gateway_url']);
        curl_setopt($CR, CURLOPT_POST, 1);
        curl_setopt($CR, CURLOPT_FAILONERROR, true);
        curl_setopt($CR, CURLOPT_POSTFIELDS, $poststring);
        curl_setopt($CR, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($CR, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($CR, CURLOPT_FAILONERROR,true);


        //actual curl execution perfom
        $result = curl_exec( $CR );
        $error = curl_error ( $CR );

        // on error - die with error message
        if( !empty( $error )) {
            die($error);
        }

        curl_close($CR);

        $xml  = simplexml_load_string((string) $result);

        $dom = new DOMDocument;
        $dom->loadXML($result);


        if($xml->response->result[0] == 000)
        {

            $cardId      = $dom->getElementsByTagName('cardId');
            $mask        = $dom->getElementsByTagName('cardMask');


            DB::useDB(B2B_DB);


            $getUser = DB::queryFirstRow("select id,smooch_id from b2b_users where smooch_id = '" . $email . "'");


            $card_id =  $cardId->item(0)->nodeValue;


            $tempp = DB::queryFirstRow("select id,card_mask from user_credit_cards WHERE card_id = '$card_id'");


            if(DB::count() == 0) {

                DB::insert('user_credit_cards', array(

                    'user_id' => $getUser['id'],
                    'card_id' => $cardId->item(0)->nodeValue,
                    'card_mask' => $mask->item(0)->nodeValue,
                    'cvv' => $cvv,
                    'expiration' => $expiry

                ));

            }

            $cards = DB::query("select id,card_mask from user_credit_cards WHERE user_id = '".$getUser['id']."'");
            $data = [

                "success" => true,  // SUCCESS
                "cards"  => $cards
            ];

            $response = $response->withStatus(202);
            $response = $response->withJson($data);
            return $response;

        }
        else{
            $bug = "";
            if($xml->response->result[0] == "001")
            {
                $bug = "The card is blocked, confiscate it. The card is blocked, confiscate it.";
            }
            else if($xml->response->result[0] == "002")
            {
                $bug = "The card is stolen, confiscate it. The card is stolen, confiscate it";
            }
            else if($xml->response->result[0] == "004")
            {
                $bug = "Refusal by credit company.";
            }
            else if($xml->response->result[0] == "005")
            {
                $bug = "The card is forged, confiscate it.";
            }
            else if($xml->response->result[0] == "006")
            {
                $bug = "Incorrect CVV/ID.";
            }
            else if($xml->response->result[0] == "007")
            {
                $bug = "Incorrect CAVV/ECI/UCAF";
            }
            else if($xml->response->result[0] == "012")
            {
                $bug = "This card is not permitted for foreign currency transactions";
            }
            else if($xml->response->result[0] == "017")
            {
                $bug = "Last 4 digits were not entered (W field).";
            }
            else if($xml->response->result[0] == "036")
            {
                $bug = "Expired card";
            }
            else{
                $bug = "Unknown Error occured, Please try again!";
            }




            $data = [

                "success"       => false,  // SUCCESS FALSE WRONG CODE
                "error"         => $bug,
                "extra_info"    => (string) $xml->response->message[0].$xml->response->result[0]

            ];

            $response = $response->withStatus(202);
            $response = $response->withJson($data);
            return $response;
        }
    }


});



//  CREDIT GUARD ACCEPT USER'PAYMENTS

$app->post('/stripe_payment_request', function ($request, $response, $args) {

    try {

        $order_data   = $request->getParam('order_data');
        $card_no      = $request->getParam('card_no');
        $exp          = $request->getParam('expiration');
        $cvv          = $request->getParam('cvv');

        $email = $order_data['user']['email'];


        DB::useDB(B2B_DB);

        $getUser = DB::queryFirstRow("select id,smooch_id from b2b_users where smooch_id = '$email'");

        if (DB::count() == 0) {


            // ERROR B2B USER NOT EXISTS

            $response =  $response->withStatus(500);
            $response =  $response->withHeader('Content-Type', 'text/html');
            $response =  $response->write("B2B User Not Exist");
            return $response;

        }
        else {

            // IF USER ALREADY EXIST IN DATABASE
            $user_id = $getUser['id'];

        }


        $cId = $order_data['selectedCardId'];

        // CARD PAYMENT THROUGH SAVE CARD CARD ID

        if($cId != null && $cId != "") {

            $card = DB::queryFirstRow("select * from user_credit_cards where id = $cId");


            if ($order_data['language'] == 'en') {


                $result = stripePaymentRequest(($order_data['total_paid'] * 100), $user_id, $email, $card['card_id'], $card['expiration'], $card['cvv'],true);


            } else {


                $result = stripePaymentRequestHE(($order_data['total_paid'] * 100), $user_id, $email, $card['card_id'], $card['expiration'], $card['cvv'],true);


            }

        }

        // CARD PAYMENT WITHOUT SAVE DIRECT

        else{


            if ($order_data['language'] == 'en') {


                $result = stripePaymentRequest(($order_data['total_paid'] * 100), $user_id, $email, $card_no, $exp, $cvv,false);


            } else {


                $result = stripePaymentRequestHE(($order_data['total_paid'] * 100), $user_id, $email, $card_no, $exp, $cvv, false);


            }



        }


        //cardNo

        // RESPONSE RETURN TO REST API CALL
        $response = $response->withStatus(202);
        $response = $response->withJson($result);
        return $response;
    }
    catch(MeekroDBException $e) {

        $response =  $response->withStatus(500);
        $response =  $response->withHeader('Content-Type', 'text/html');
        $response =  $response->write( $e->getMessage());
        return $response;
    }


});



// SUPPORT METHODS
// STRIPE API PAYMENT REQUEST
// AMOUNT DIVIDED BY 100 FROM API

function  stripePaymentRequest($amount, $user_id, $email ,$creditCardNo, $expDate, $cvv ,$isUseCardId)
{
    $rest = "Error";
    $cgConf['tid']='8804324';
    $cgConf['amount']=$amount;
    $cgConf['user']='pushstart';
    $cgConf['password']='OE2@38sz';
    $cgConf['cg_gateway_url']="https://cgpay5.creditguard.co.il/xpo/Relay";

    $poststring = 'user='.$cgConf['user'];
    $poststring .= '&password='.$cgConf['password'];

    /*Build Ashrait XML to post*/
    $poststring.='&int_in=<ashrait>
							<request>
							<language>ENG</language>
							<command>doDeal</command>
							<requestId/>
							<version>1000</version>
							<doDeal>
								<terminalNumber>'.$cgConf['tid'].'</terminalNumber>
								<authNumber/>
								<transactionCode>Phone</transactionCode>
								<transactionType>Debit</transactionType>
								<total>'.$cgConf['amount'].'</total>
								<creditType>RegularCredit</creditType>';


    if($isUseCardId)
    {
        $poststring .= '<cardId>'.$creditCardNo.'</cardId>';
    }
    else{

        $poststring .= '<cardNo>'.$creditCardNo.'</cardNo>';
    }


    $poststring .=	'<cvv>'.$cvv.'</cvv>
								<cardExpiration>'.$expDate.'</cardExpiration>
								<validation>AutoComm</validation>
								<numberOfPayments/>
								<customerData>
									<userData1>'.$email.'</userData1>
									<userData2/>
									<userData3/>
									<userData4/>
									<userData5/>
								</customerData>
								<currency>ILS</currency>
								<firstPayment/>
								<periodicalPayment/>
								<user>Push</user>	
								
								<invoice>

									<invoiceCreationMethod>wait</invoiceCreationMethod>
									
									<invoiceDate/>
									
									<invoiceSubject> Order# '.$user_id.'</invoiceSubject>
									
									<invoiceDiscount/>
									
									<invoiceDiscountRate/>
									
									<invoiceItemCode>'.$user_id.'</invoiceItemCode>
									
									<invoiceItemDescription> Food Order </invoiceItemDescription>
									
									<invoiceItemQuantity>1</invoiceItemQuantity>
									
									<invoiceItemPrice>'.$amount.'</invoiceItemPrice>
									
									<invoiceTaxRate/>
									
									<invoiceComments/>
									
									<companyInfo>OrderApp</companyInfo>
									
									<sendMail>1</sendMail>
									
									<mailTo>'.$email.'</mailTo>
									
									<isItemPriceWithTax>0</isItemPriceWithTax>
									
									<ccDate>'.date("Y-m-d").'</ccDate>
									
									<invoiceSignature/>
									
									<invoiceType>receipt</invoiceType>
									
									<DocNotMaam/>
									
								</invoice>	
								
							</doDeal>
						</request>
					</ashrait>';


    //init curl connection
    if( function_exists( "curl_init" )) {
        $CR = curl_init();
        curl_setopt($CR, CURLOPT_URL, $cgConf['cg_gateway_url']);
        curl_setopt($CR, CURLOPT_POST, 1);
        curl_setopt($CR, CURLOPT_FAILONERROR, true);
        curl_setopt($CR, CURLOPT_POSTFIELDS, $poststring);
        curl_setopt($CR, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($CR, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($CR, CURLOPT_FAILONERROR,true);


        //actual curl execution perfom
        $result = curl_exec( $CR );
        $error = curl_error ( $CR );

        // on error - die with error message
        if( !empty( $error )) {
            die($error);
        }

        curl_close($CR);

        $xml  = simplexml_load_string((string) $result);

        if($xml->response->result[0] == 000)
        {
            $rest = [

                "response" => 'success',
                "trans_id" => (string) $xml->response->tranId[0]
            ];

        }
        else{

//            if((string) $xml->response->message[0] == "Card id was not found.")
//            {
//                $bug = "Card Invalid";
//            }
//            else if((string) $xml->response->message[0] == "Incorrect control number.")
//            {
//                $bug =  "Card Number Is Invalid";
//            }
//            else if((string) $xml->response->message[0] == "Incorrect CVV/ID.")
//            {
//                $bug =  "CVV is invalid";
//            }
//
//            else if((string) $xml->response->message[0] == "An XML field or an INT_IN parameter is too short/ long.")
//            {
//                $bug =  "Expiration Date or Card Number is invalid";
//            }
//            else{
//                $bug = "Unknown Error occured, Please try again!";
//            }

            if($xml->response->result[0] == "001")
            {
                $bug = "The card is blocked, confiscate it. The card is blocked, confiscate it.";
            }
            else if($xml->response->result[0] == "002")
            {
                $bug = "The card is stolen, confiscate it. The card is stolen, confiscate it";
            }
            else if($xml->response->result[0] == "004")
            {
                $bug = "Refusal by credit company.";
            }
            else if($xml->response->result[0] == "005")
            {
                $bug = "The card is forged, confiscate it.";
            }
            else if($xml->response->result[0] == "006")
            {
                $bug = "Incorrect CVV/ID.";
            }
            else if($xml->response->result[0] == "007")
            {
                $bug = "Incorrect CAVV/ECI/UCAF";
            }
            else if($xml->response->result[0] == "012")
            {
                $bug = "This card is not permitted for foreign currency transactions";
            }
            else if($xml->response->result[0] == "017")
            {
                $bug = "Last 4 digits were not entered (W field).";
            }
            else if($xml->response->result[0] == "036")
            {
                $bug = "Expired card";
            }
            else{
                $bug = "Unknown Error occured, Please try again!";
            }

            $rest = [

                "response" =>  $bug,
                "extra_info"    => (string) $xml->response->message[0].$xml->response->result[0]

            ];

        }

    }

    return $rest;

}


//  ADD USER ORDER TO SERVER



//  STORE USER ORDER IN DATABASE
$app->post('/b2b_add_order', function ($request, $response, $args) {


    DB::useDB(B2B_DB);

    // GET ORDER RESPONSE FROM USER (CLIENT SIDE)
    $user_order = $request->getParam('b2b_user_order');

    $user_id    = null;
    $user_id  = $user_order['user']['user_id'];


    date_default_timezone_set("Asia/Jerusalem");
    $todayDate  = Date("d/m/Y");
    $today = date("Y-m-d");


    DB::useDB(B2B_DB);
    //CHECK IF USER ALREADY EXIST, IF NO CREATE USER
    $getUser = DB::queryFirstRow("select * from b2b_users where id = '" . $user_id . "'");


    $discount = $getUser['discount'] - $user_order['company_contribution'];



    // CREATE A NEW ORDER AGAINST USER
    DB::useDB(B2B_DB);

    date_default_timezone_set("Asia/Jerusalem");
    $curr = date("Y-m-d H:i:s");




    DB::insert('b2b_orders', array(
        'user_id'                       => $user_order['user']['user_id'],
        'company_id'                    => $user_order['company']['company_id'],
        'total'                         => $user_order['total_paid'],
        'actual_total'                  => $user_order['actual_total'],
        'discount'                      => $user_order['discount'],
        'company_contribution'          => $user_order['company_contribution'],
        'transaction_id'                => $user_order['transactionId'],
        'restaurant_id'                 => $user_order['rests_orders'][0]['selectedRestaurant']['id'],
        "date"                          => $curr,
        "rest_order_object"             => json_encode($user_order),
        "payment_info"                  => $user_order['payment_option'],
        "platform_info"                 => $user_order['platform_info'],
        "browser_info"                  => $user_order['browser_info'],
        "ignore_old_reorder"            => "false",
    ));
    $orderId = DB::insertId();


    date_default_timezone_set("Asia/Jerusalem");
    $onlyDate = date("d-m-Y");              //FOR LEDGER
    $onlytime = date("H:i");              //FOR LEDGER
    DB::useDB(B2B_DB);
    DB::insert('b2b_ledger', array(
        'date'                          => $onlyDate,
        'time'                          => $onlytime,
        'customer_name'                 => $getUser['name'],
        'customer_contact'              => $getUser['contact'],
        'customer_email'                => $user_order['user']['email'],
        'restaurant_name'               => $user_order['rests_orders'][0]['selectedRestaurant']['name_en'],
        'payment_method'                => $user_order['payment_option'],
        'delivery_or_pickup'            => 'Delivery',
        'delivery_price'                => '0',
        'company_name'                  => $user_order['company']['company_name'],
        "order_no"                      => $orderId,
        "discount_amount"               => $user_order['discount'],
        "restaurant_total"              => $user_order['actual_total'],
        "customer_grand_total"          => $user_order['total_paid'],
        "customer_total_paid_to_restaurant"  => $user_order['total_paid'],
        "eluna"                         => "false",
    ));



    //GET COMPANY NAME
    DB::useDB(B2B_DB);

    $getCompanyName = DB::queryFirstRow("select * from company where id = '" . $user_order['company']['company_id'] . "'");
    $user_order['company']['discount_type'] = $getCompanyName['discount_type'];

    //TRACCER CODE
    try
    {
        traccer($orderId, $user_order['user']['name'], $user_order['user']['contact'], $user_order['rests_orders'][0]['selectedRestaurant']['address_en'], $user_order['company']['company_address'], $user_order['rests_orders'][0]['selectedRestaurant']['rest_lat'], $user_order['rests_orders'][0]['selectedRestaurant']['rest_lng'], $user_order['company']['lat'], $user_order['company']['lng']);

    }
    catch (Exception $exception)
    {


    }



    // LAST INSERTED ORDER ID
    foreach($user_order['rests_orders'][0]['foodCartData'] as $orders)
    {
        try{


            // ADD ORDER DETAIL AGAINST USER
            DB::useDB(B2B_DB);


            DB::insert('b2b_order_detail', array(

                'order_id' => $orderId,
                'qty' => $orders['qty'],
                'item' => $orders['name'],
                'sub_total' => $orders['price'],
                'sub_items' => $orders['detail']

            ));


        }
        catch(MeekroDBException $e) {

            $response =  $response->withStatus(500);
            $response =  $response->withHeader('Content-Type', 'text/html');
            $response =  $response->write( $e->getMessage());
            return $response;

        }

    }

    // EMAIL ORDER SUMMERY

    email_for_kitchen($user_order, $orderId, $todayDate);
    ob_end_clean();



    // EMAIL FOR LEDGER
    email_for_mark2($user_order, $orderId, $todayDate);
    ob_end_clean();


    // SEND ADMIN COPY EMAIL ORDER SUMMARY

    email_order_summary_hebrew_admin($user_order, $orderId, $todayDate);
    ob_end_clean();


    // CLIENT EMAIL
    // EMAIL ORDER SUMMARY
    //
    if ($user_order['language'] == 'en')
    {

        email_order_summary_english($user_order, $orderId, $todayDate);

    }
    else
    {

        email_order_summary_hebrew($user_order, $orderId, $todayDate);

    }

    ob_end_clean();




    DB::useDB(B2B_DB);

    DB::query("UPDATE b2b_users SET date = '$today', discount = '$discount'  WHERE  id = '$user_id'");


    // RESPONSE RETURN TO REST API CALL
    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode('success'));
    return $response;
});




// GET ALL USER CREDIT CARDS AVAILABLE


$app->post('/get_all_cards_info', function ($request, $response, $args){


    $user_email = $request->getParam('user_email');

    DB::useDB(B2B_DB);


    //CHECK IF USER ALREADY EXIST, IF NO CREATE USER
    $getUser = DB::queryFirstRow("select id,smooch_id from b2b_users where smooch_id = '" . $user_email . "'");

    DB::useDB(B2B_DB);
    $cards = DB::query("select id,card_mask from user_credit_cards WHERE user_id = '".$getUser['id']."'");


    if(DB::count() == 0) {

        $response = $response->withStatus(202);
        $response = $response->withJson('null');
        return $response;

    }

    $response = $response->withStatus(202);
    $response = $response->withJson($cards);
    return $response;



});


//  CHECK HIDE PAYMENT OPTION FOR COMPANY
$app->post('/is_hide_payment', function ($request, $response, $args)
{

    DB::useDB(B2B_DB);

    $company_id = $request->getParam('company_id');
    $companyDetail = DB::queryFirstRow("select * from company where id = '$company_id'");

    // RESPONSE RETURN TO REST API CALL
    $response = $response->withStatus(202);
    $response = $response->withJson($companyDetail['hide_payment']);
    return $response;


});

// CANCEL ORDER
$app->post('/cancel_order', function ($request, $response, $args)
{
    DB::useDB(B2B_DB);
    $order_id = $request->getParam('order_id');




    DB::useDB(B2B_DB);
    $rest_object = DB::queryFirstRow("select * from b2b_orders  WHERE  id = '$order_id'");
    $day = date('l');


    DB::useDB(B2B_DB);
    $delivery_timing = DB::queryFirstRow("select delivery_timing from company_timing where company_id =  '".$rest_object['company_id']."' and week_en = '$day'");

    //CHECK THE CANCEL ORDER TIME IS WITHIN 30 MINUTES
    date_default_timezone_set("Asia/Jerusalem");
    $to_time = strtotime(date('H:i:s'));
    $from_time = strtotime($delivery_timing['delivery_timing'].":00");

    $delivery_time =  intval(abs($to_time - $from_time) / 60);


    // CANCEL THE ORDER
    if($delivery_time >= 30) {

        // CREATE A NEW ORDER AGAINST USER
        DB::useDB(B2B_DB);
        DB::query("UPDATE b2b_orders SET order_status = 'cancelled'  WHERE  id = '$order_id'");




        $todayDate = Date("d/m/Y");
        $today = date("Y-m-d");


        $user_order = json_decode($rest_object['rest_order_object']);


        $company_contribution = $user_order->company_contribution;


        DB::useDB(B2B_DB);
        $user = DB::queryFirstRow("select * from b2b_users  WHERE  id = '" . $rest_object['user_id'] . "'");
        $remaining_discount = $company_contribution + $user['discount'];


        //UPDATE THE DISCOUNT OF USE AFTER CANCLLING ORDER
        DB::useDB(B2B_DB);
        DB::query("UPDATE b2b_users SET discount = '$remaining_discount'  WHERE  id = '".$user['id']."'");


        // EMAIL ORDER SUMMERY

        email_for_kitchen_cancel($user_order, $order_id, $todayDate);
        ob_end_clean();


        // EMAIL FOR LEDGER

        email_for_mark2_cancel($user_order, $order_id, $todayDate);
        ob_end_clean();


        // SEND ADMIN COPY EMAIL ORDER SUMMARY

        //email_order_summary_hebrew_admin($user_order, $orderId, $todayDate);
        // ob_end_clean();


        // CLIENT EMAIL
        // EMAIL ORDER SUMMARY


        if ($user_order->language == "en") {


            email_order_summary_english_cancel($user_order, $order_id, $todayDate, $remaining_discount);
            ob_end_clean();

        } else {

            //  email_order_summary_hebrew($user_order, $orderId, $todayDate);

        }

        ob_end_clean();

        // RESPONSE RETURN TO REST API CALL
        $response = $response->withStatus(202);
        $response = $response->withJson($remaining_discount);
        return $response;
    }
    // USER CANCEL THE ORDER AFTER THE 30 MINUTES OF ORDER TIME
    else{

        // RESPONSE RETURN TO REST API CALL
        $response = $response->withStatus(202);
        $response = $response->withJson('false');
        return $response;
    }

});




//  SEND ERROR REPORT TO DEVELOPMENT TEAM
$app->post('/error_report', function ($request, $response, $args){


    DB::useDB('orderapp_b2b_wui');

    $msg = '';
    $host    =  $request->getParam('host');
    $url     =  $request->getParam('url');
    $message =  $request->getParam('message');


    sendReportToDevTeam($host,$url,$message);

    ob_end_clean();

    $response = $response->withStatus(202);
    $response = $response->withJson("true");
    return $response;

});






// SAVE CATEGORY IMAGE FOR ADMIN PANEL UPDATE MAIN DB (ORDER APP)
$app->post('/save_category_image', function ($request, $response, $args)
{
    $resp = "";
    $data = '';

    $id = $request->getParam('cat_id');
    $menu_id = $request->getParam('menu_id');

    $rests = DB::queryFirstRow("select * from categories where id = '".$id."'");
    if(DB::count() != 0) {
        $data = $rests['image_url'];
    }
    $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));


    $menu_id = DB::queryFirstRow("select restaurant_id from menus where id = '".$menu_id."'");

    $restaurant = DB::queryFirstRow("select * from restaurants where id = '".$menu_id['restaurant_id']."'");


    $restaurant['name_en'] = preg_replace('/\s*/', '', $restaurant['name_en']);

    $restaurant['name_en'] = strtolower($restaurant['name_en']);


    $rests['name_en'] = preg_replace('/\s*/', '', $rests['name_en']);

    $rests['name_en'] = strtolower($rests['name_en']);



    //"/m/en/img/categories/".$restaurant['name_en']."/".$_POST['name_en'].".png"
    if(!is_dir("../m/en/img/categories/" . $restaurant['name_en']))
    {
        mkdir("../m/en/img/categories/" . $restaurant['name_en'], 0777);

    }


    $filepath = "../m/en/img/categories/".$restaurant['name_en']."/".$rests['name_en'].".png"; // or image.jpg


    $image_url = "";
    if(file_put_contents($filepath,$data))
    {
        $image_url = "/m/en/img/categories/".$restaurant['name_en']."/".$rests['name_en'].".png";
        $resp =  "working";

    }
    else{

        $resp = "not workign";
        $image_url = "/m/en/img/cs-category.png";
    }
    DB::query("update categories set image_url = '".$image_url."' where id = '$id'");

    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($resp));
    return $response;

});




// UPDATE DATA ENTERY DB

$app->post('/save_category_image_dataentry', function ($request, $response, $args)
{
    global $con;
    $resp = "";
    $data = '';

    $id = $request->getParam('cat_id');
    $menu_id = $request->getParam('menu_id');


    // CATEGORY IMAGE URL
    $get_brand = "select * from categories where id = '".$id."'";
    $run_brand = mysqli_query($con, $get_brand);

    while($row_brand = mysqli_fetch_array($run_brand))
    {
        $data = $row_brand['image_url'];
        $name  = $row_brand['name_en'];

    }

    $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));


    $get_brand = "select restaurant_id from menus where id = '".$menu_id."'";
    $run_brand = mysqli_query($con, $get_brand);

    while($row_brand2 = mysqli_fetch_array($run_brand))
    {
        $menu_id  = $row_brand2['restaurant_id'];

        $get_brand = "select * from restaurants where id = '".$menu_id."'";
        $run_brand = mysqli_query($con, $get_brand);

        while($row_brand1 = mysqli_fetch_array($run_brand))
        {
            //$data = $row_brand['image_url'];
            $restaurant['name_en']  = $row_brand1['name_en'];

        }


    }


    $restaurant['name_en'] = preg_replace('/\s*/', '', $restaurant['name_en']);

    $restaurant['name_en'] = strtolower($restaurant['name_en']);


    $rests['name_en'] = preg_replace('/\s*/', '', $name);

    $rests['name_en'] = strtolower($rests['name_en']);



    //"/m/en/img/categories/".$restaurant['name_en']."/".$_POST['name_en'].".png"
    if(!is_dir("../m/en/img/categories/" . $restaurant['name_en']))
    {
        mkdir("../m/en/img/categories/" . $restaurant['name_en'], 0777);

    }


    $filepath = "../m/en/img/categories/".$restaurant['name_en']."/".$rests['name_en'].".png"; // or image.jpg


    $image_url = "";
    if(file_put_contents($filepath,$data))
    {
        $image_url = "/m/en/img/categories/".$restaurant['name_en']."/".$rests['name_en'].".png";
        $resp =  "working";

    }
    else{

        $resp = "not workign";
        $image_url = "/m/en/img/cs-category.png";
    }

    $update_order = "update categories set image_url = '".$image_url."' where id = '$id'";
    $run_order = mysqli_query($con, $update_order);

    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($resp));
    return $response;

});




$app->post('/update_restaurant_logo', function ($request, $response, $args)
{
    $resp = "";
    $data = '';

    $id = $request->getParam('rest_id');

    $rests = DB::queryFirstRow("select * from restaurants where id = '".$id."'");

    if(DB::count() != 0) {

        $data = $rests['logo'];

    }

    $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));

    $name_logo = preg_replace('/\s*/', '', $rests['name_en']);

    $name_logo = strtolower($name_logo);

    if (!is_dir("/m/en/img"))
    {
        mkdir("/m/en/img", 0777);

    }

    $filepath = "/m/en/img/".$name_logo."_logo.png"; // or image.jpg


    $image_url = "";
    if(file_put_contents($filepath,$data))
    {
        $image_url = "/m/en/img/".$name_logo."_logo.png";
        $resp =  "workingg";

    }
    else
    {

        $resp = "not working";
        $image_url = "/m/en/img/cs-logo.png";

    }
    DB::query("update restaurants set logo = '".$image_url."' where id = '$id'");




    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($image_url));
    return $response;

});



$app->post('/insert_new_restaurant_dataentry', function ($request, $response, $args)
{

    global $con;

    $resp = "";
    $data = '';

    $id = $request->getParam('rest_id');



    $get_brand = "select * from restaurants where id = '".$id."'";
    $run_brand = mysqli_query($con, $get_brand);

    while($row_brand = mysqli_fetch_array($run_brand))
    {

        $data = $row_brand['logo'];
        $name  = $row_brand['name_en'];

    }


    $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));

    $name_logo = preg_replace('/\s*/', '', $name);

    $name_logo = strtolower($name_logo);

    $filepath = "../m/en/img/".$name_logo."_logo.png"; // or image.jpg


    $image_url = "";
    if(file_put_contents($filepath,$data))
    {
        $image_url = "/m/en/img/".$name_logo."_logo.png";
        $resp =  "workingg";

    }
    else
    {

        $resp = "not working";
        $image_url = "/m/en/img/cs-logo.png";

    }

    $update_order = "update restaurants set logo = '".$image_url."' where id = '$id'";
    $run_order = mysqli_query($con, $update_order);


    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($resp));
    return $response;

});



$app->post('/insert_new_restaurant', function ($request, $response, $args)
{
    $resp = "";
    $data = '';

    $id = $request->getParam('rest_id');

    $rests = DB::queryFirstRow("select * from restaurants where id = '".$id."'");

    if(DB::count() != 0) {

        $data = $rests['logo'];

    }

    $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));

    $name_logo = preg_replace('/\s*/', '', $rests['name_en']);

    $name_logo = strtolower($name_logo);

    $filepath = "../m/en/img/".$name_logo."_logo.png"; // or image.jpg


    $image_url = "";
    if(file_put_contents($filepath,$data))
    {
        $image_url = "/m/en/img/".$name_logo."_logo.png";
        $resp =  "workingg";

    }
    else
    {

        $resp = "not working";
        $image_url = "/m/en/img/cs-logo.png";

    }
    DB::query("update restaurants set logo = '".$image_url."' where id = '$id'");




    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($resp));
    return $response;

});






$app->run();


