<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require      'vendor/autoload.php';
require      'PHPMailer/PHPMailerAutoload.php';
require_once 'inc/initDb.php';

use Voucherify\VoucherifyClient;
use Voucherify\VoucherBuilder;
use Voucherify\CustomerBuilder;
use Voucherify\ClientException;


DB::query("set names utf8");


// EMAIL SERVERS FOR EACH EMAIL ADDRESS

// DEV SERVER
if($_SERVER['HTTP_HOST'] == "dev.orderapp.com")
{
    define("EMAIL","devorders@orderapp.com");
    define("B2BEMAIL","devb2b.orderapp.com");
}

// QA SERVER
else if($_SERVER['HTTP_HOST'] == "qa.orderapp.com"){

    define("EMAIL","qaorders@orderapp.com");
    define("B2BEMAIL","qab2b.orderapp.com");
}


// PRODUCTION SERVER
else
{
    define("EMAIL","orders@orderapp.com");
    define("B2BEMAIL","b2b.orderapp.com");
}



// SLIM INITIALIZATION
$app = new \Slim\App();
$app = new \Slim\App();


//  GET LIST OF CITIES
//  WEB HOOK GET LIST OF CITIES AVAILABLE
$app->post('/get_all_cities', function ($request, $response, $args)
{
    try{

        // MINIMUM ORDER AMOUNT
        $cities = DB::query("select * from cities");

        // RESPONSE RETURN TO REST API CALL
        $response = $response->withStatus(202);
        $response = $response->withJson(json_encode($cities));
        return $response;


    }
    catch(MeekroDBException $e) {

        $response =  $response->withStatus(500);
        $response =  $response->withHeader('Content-Type', 'text/html');
        $response =  $response->write( $e->getMessage());
        return $response;
    }

});



//  WEB HOOK GET MINIMUM ORDER AMOUNT
$app->post('/get_min_order_amount', function ($request, $response, $args)
{
    try {

        // MINIMUM ORDER AMOUNT
        $minOrder = DB::query("select * from default_settings where name = 'min_order'");

        // RESPONSE RETURN TO REST API CALL
        $response = $response->withStatus(202);
        $response = $response->withJson(json_encode($minOrder));
        return $response;
    }
    catch(MeekroDBException $e) {

        $response =  $response->withStatus(500);
        $response =  $response->withHeader('Content-Type', 'text/html');
        $response =  $response->write( $e->getMessage());
        return $response;
    }
});



//  WEB HOOK GET ALL RESTAURANT
$app->post('/get_all_restaurants', function ($request, $response, $args)
{

    try {

        $id = $request->getParam('city_id');


        $restaurants = Array();

        $results = DB::query("select * from restaurants  where city_id = '$id' and hide = 0 order by sort ASC ");

        $count = 0;

        foreach ($results as $result) {
            // GET TAGS OF RESTAURANT i.e BURGER , PIZZA

            $tagsIds = DB::query("select tag_id from restaurant_tags where restaurant_id = '" . $result['id'] . "'");

            $tags = Array();
            $count2 = 0;
            $hoursLeftToOpen = null;

            foreach ($tagsIds as $id) {

                $tag = DB::queryFirstRow("select * from tags where id = '" . $id["tag_id"] . "'");
                $tags[$count2] = $tag;
                $count2++;
            };

            // GET GALLERY OF RESTAURANT

            $galleryImages = DB::query("select url from restaurant_gallery where restaurant_id = '" . $result['id'] . "'");


            // RETRIEVING RESTAURANT TIMINGS i.e SUNDAY   STAT_TIME : 12:00  END_TIME 21:00;

            $restaurantTimings = DB::query("select * from weekly_availibility where restaurant_id = '" . $result['id'] . "'");

            // CURRENT TIME OF ISRAEL
            date_default_timezone_set("Asia/Jerusalem");
            $currentTime = date("H:i");
            $tempDate = date("d/m/Y");
            $dayOfWeek = date('l');

            // RESTAURANT AVAILABILITY ACCORDING TO TIME
            $currentStatus = false;

            $today_timings = "";
            $today_timings_he = "";


            foreach ($restaurantTimings as $singleTime) {


                if ($singleTime['week_en'] == $dayOfWeek) {


                    $today_timings = $singleTime['opening_time'] . " - " . $singleTime['closing_time'];
                    $today_timings_he = $singleTime['opening_time_he'] . " - " . $singleTime['closing_time_he'];
                    $openingTime = DateTime::createFromFormat('H:i', $singleTime['opening_time']);
                    $closingTime = DateTime::createFromFormat('H:i', $singleTime['closing_time']);
                    $currentTime = DateTime::createFromFormat('H:i', $currentTime);


                    if ($currentTime >= $openingTime && $currentTime <= $closingTime) {

                        $currentStatus = true;

                        break;
                    } else {

                        $hoursLeftToOpen = "Open On Sunday";


                    }

                }
            }

            $delivery_fee = DB::query("select * from delivery_fee where restaurant_id = '".$result['id']."'");

            $min = $delivery_fee[0]["fee"];
            $max = $delivery_fee[0]["fee"];

            // CALCULATING MINIMUM AND MAXIMUM DELIVERY FEE
            foreach ($delivery_fee as $fee) {
                if ($fee['fee'] > $max)
                    $max = $fee['fee'];
                else if ($fee['fee'] < $min)
                    $min = $fee['fee'];
            }


            // CREATE DEFAULT RESTAURANT OBJECT;
            $restaurant = [

                "min_delivery"         => $min,                         // MINIMUM DELIVERY FEE
                "max_delivery"         => $max,                         // MAXIMUM DELIVERY FEE
                "id"                   => $result["id"],                // RESTAURANT ID
                "name_en"              => $result["name_en"],           // RESTAURANT NAME
                "name_he"              => $result["name_he"],           // RESTAURANT NAME
                "min_amount"           => $result["min_amount"],        // RESTAURANT MINIMUM AMOUNT
                "logo"                 => $result["logo"],              // RESTAURANT LOGO
                "description_en"       => $result["description_en"],    // RESTAURANT DESCRIPTION
                "description_he"       => $result["description_he"],    // RESTAURANT DESCRIPTION
                "address_en"           => $result["address_en"],        // RESTAURANT ADDRESS
                "address_he"           => $result["address_he"],        // RESTAURANT ADDRESS
                "hechsher_en"          => $result["hechsher_en"],       // RESTAURANT HECHSHER
                "hechsher_he"          => $result["hechsher_he"],       // RESTAURANT HECHSHER
                "coming_soon"          => $result["coming_soon"],       // RESTAURANT COMING SOON
                "pickup_hide"          => $result["pickup_hide"],       // HIDE PICK UP OPTION
                "tags"                 => $tags,                        // RESTAURANT TAGS
                "gallery"              => $galleryImages,               // RESTAURANT GALLERY
                "timings"              => $restaurantTimings,           // RESTAURANT WEEKLY TIMINGS
                "availability"         => $currentStatus,               // RESTAURANT CURRENT AVAILABILITY
                "today_timings"        => $today_timings,               // TODAY TIMINGS
                "today_timings_he"     => $today_timings_he,            // TODAY TIMINGS HE
                "hours_left_to_open"   => $hoursLeftToOpen,             // HOURS LEFT TO OPEN FROM CURRENT TIME
                "delivery_fee"         => $delivery_fee,                // DELIVERY FEE AREA WISE
                "contact"              => $result['contact']            // CONTACT NO
            ];

            $restaurants[$count] = $restaurant;
            $count++;
        }


        // RESPONSE RETURN TO REST API CALL
        $response = $response->withStatus(202);
        $response = $response->withJson(json_encode($restaurants));
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

        // GET MENUS FOR RESTAURANT i.e LUNCH
        $menu = DB::queryFirstRow("select * from menus where restaurant_id = '" . $id . "'");

        // GET CATEGORIES OF RESTAURANT i.e ANGUS SALAD , ANGUS BURGER
        $categories = DB::query("select * from categories where menu_id = '" . $menu['id'] . "'");

        $count2 = 0;

        $items = '';

        foreach ($categories as $category) {

            $items = array();

            if($category['business_offer'] == 0) {

                $items = DB::query("select * from items where category_id = '" . $category["id"] . "' and hide = '0'");

            }
            else {

//                // BUSINESS LUNCH CATEGORY GET SELECTED ITEMS
                $first_day_this_month = date('Y-m-01');
                $firstDayOfMonth = $first_day_this_month;

                $currentDate = date('Y-m-d');

                $dtCurrent      = DateTime::createFromFormat('Y-m-d', $currentDate);
                $dtFirstOfMonth = DateTime::createFromFormat('Y-m-d', $firstDayOfMonth);

                $numWeeks = 1 + (intval($dtCurrent->format("W")) - intval($dtFirstOfMonth->format("W")));

                $dayOfWeek = date('l');

                $businessItemsIds = DB::query("select item_id from business_lunch_detail where category_id = '" . $category["id"] . "' AND  week_day = '$dayOfWeek' AND week_cycle = '$numWeeks'");


                foreach ($businessItemsIds as $businessItem) {

                    $item = DB::queryFirstRow("select * from items where category_id = '" . $category["id"] . "' and hide = '0' and id = '".$businessItem['item_id']."'");

                    array_push($items, $item);
                }

            }

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

        // CREATE DEFAULT OBJECT FOR ITEMS AND CATEGORIES;
        $data = [
            "menu_name_en" => $menu['name_en'],               // MENU NAME EN
            "menu_name_he" => $menu['name_he'],               // MENU NAME HE
            "categories_items" => $categories                 // CATEGORIES AND ITEMS
        ];


        // RESPONSE RETURN TO REST API CALL
        $response = $response->withStatus(202);
        $response = $response->withJson(json_encode($data));
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
        $response = $response->withJson(json_encode($data));
        return $response;
    }
    catch(MeekroDBException $e) {

        $response =  $response->withStatus(500);
        $response =  $response->withHeader('Content-Type', 'text/html');
        $response =  $response->write( $e->getMessage());
        return $response;
    }

});


// VALIDATE COUPONS

$app->post('/coupon_validation', function ($request, $response, $args) {


    try {

        $email              =   $request->getParam('email');          //  GET USER EMAIL
        $coupon_code        =   $request->getParam('code');           //  COUPON CODE ENTER BY USER
        $order_amount       =   $request->getParam('total');          //  ORDER AMOUNT
        $restaurant_title   =   $request->getParam('rest_title');     //  RESTAURANT TITLE
        $restaurant_city    =   $request->getParam('rest_city');      //  RESTAURANT CITY
        $delivery_fee       =   $request->getParam('delivery_fee');   //  DELIVERY FEE
        $user_order         =   $request->getParam('user_order');     //  USER ORDER
        $success_validation =   "false";                              //  SUCCESS VALIDATION RESPONSE FOR USER
        $user_id            =   null;

        $isUniqueUser       =   false;


        //CHECK IF USER ALREADY EXIST, IF NO CREATE USER
        $getUser = DB::queryFirstRow("select id,smooch_id from users where smooch_id = '$email'");

        if (DB::count() == 0) {

            // USER NOT EXIST IN DATABASE, SO CREATE USER IN DATABASE
            DB::insert('users', array(
                'smooch_id' => $email
            ));

            $user_id = DB::insertId();

            $isUniqueUser = true;
        }
        else {

            // IF USER ALREADY EXIST IN DATABASE
            $user_id = $getUser['id'];
        }

        // COUPON VALIDATION
        $coupon_code = strtoupper($coupon_code);

        $res = VoucherifyValidation($coupon_code,$user_id,($order_amount * 100),$restaurant_title,$restaurant_city,$delivery_fee,$user_order,$isUniqueUser);


        // RESPONSE RETURN TO REST API CALL
        $response = $response->withStatus(202);
        $response = $response->withJson(json_encode($res));
        return $response;

    }
    catch(MeekroDBException $e) {

        $response =  $response->withStatus(500);
        $response =  $response->withHeader('Content-Type', 'text/html');
        $response =  $response->write( $e->getMessage());
        return $response;
    }

});



function VoucherifyValidation($userCoupon,$user_id,$order_amount,$rest_title,$rest_city,$delivery_fee,$user_order,$isUniqueUser)
{

    $apiID          = "6243c07e-fea0-4f0d-89f8-243d589db97b";
    $apiKey         = "ac0d95c8-b5fd-4484-a697-41a1a91f3dd2";
    $voucherify     =  new VoucherifyClient($apiID, $apiKey);
    $data = '';


    $result = DB::queryFirstRow("select * from users where id = '$user_id'");

    $Vid = $result['voucherify_id'];



    // USER NOT EXIST ON VOUCHERIFY
    if($Vid == "" || $Vid == null)
    {

        try {

            $customer = (new CustomerBuilder())
                ->setName($result['name'])
                ->setEmail($result['email'])
                ->setDescription("OrderApp website2.0 User")
                ->setMetadata((object)array("lang" => "en"))
                ->build();

            $vResult = $voucherify->customer->create($customer);


            DB::query("update users set voucherify_id = '".$vResult->id."' where id = '$user_id'");


            $Vid =  $vResult->id;


        }
        catch (ClientException $e)
        {
            $data = [

                "success" => false  // SUCCESS FALSE WRONG CODE

            ];
            return $data;
        }
    }


    try {

        $result =  $voucherify->get($userCoupon);

        if($result->discount->type == "UNIT" && $delivery_fee == "null")
        {
            $data = [

                "success" => false,                                       // COUPON VALID OR NOT (TRUE OR FALSE)
                "deliveryFree" => true,                                   //  COUPON DISCOUNT
                "message" => "Error Coupon is valid only in case of Delivery"
            ];


            return $data;

        }



        // PREPARE META DATA ON ITEMS SELECTED
        // FOR COUPON REDUMPTION VALIDATION

        $metaData = [

            "restaurant" => $rest_title,
            "city" => $rest_city,
            "unique user" => $isUniqueUser

        ];


        foreach ($user_order['orders'] as $order) {

            $metaData[$order['itemName']] =  $order['itemName'];
        }




        $resultRedeem = $voucherify->redeem([
            "voucher" => $userCoupon,
            "customer" => [
                "id" =>  $Vid
            ],
            "order" => [
                "amount" => $order_amount
            ],
            "metadata" => $metaData

        ], NULL);



        if($resultRedeem->voucher->discount->type == "AMOUNT")
        {
            $dAmount = ($resultRedeem->voucher->discount->amount_off / 100);

            $data = [

                "success" => true,                                         // COUPON VALID OR NOT (TRUE OR FALSE)
                "amount" => $dAmount,                                      // DISCOUNT ON COUPON
                "isFixAmountCoupon" => true                                // COUPON TYPE (AMOUNT OR PERCENTAGE)

            ];


            return $data;


        }
        else if($resultRedeem->voucher->discount->type == "PERCENT")
        {
            $dAmount = $resultRedeem->voucher->discount->percent_off;


            $data = [

                "success" => true,                                         // COUPON VALID OR NOT (TRUE OR FALSE)
                "amount" => $dAmount,                                      // DISCOUNT ON COUPON
                "isFixAmountCoupon" => false,                              // COUPON TYPE (AMOUNT OR PERCENTAGE)
                "deliveryFree" => false
            ];


            return $data;
        }
        else if($resultRedeem->voucher->discount->type == "PERCENT")
        {
            $dAmount = $resultRedeem->voucher->discount->percent_off;


            $data = [

                "success" => true,                                         // COUPON VALID OR NOT (TRUE OR FALSE)
                "amount" => $dAmount,                                      // DISCOUNT ON COUPON
                "isFixAmountCoupon" => false,                             // COUPON TYPE (AMOUNT OR PERCENTAGE)
                "deliveryFree" => false
            ];


            return $data;
        }
        else if($resultRedeem->voucher->discount->type == "UNIT")
        {
            $data = [

                "success" => true,                                         // COUPON VALID OR NOT (TRUE OR FALSE)
                "deliveryFree" => true,                                    //  COUPON DISCOUNT

            ];

            return $data;
        }


    }
    catch (ClientException $e)
    {
        $data = [

            "success" => false,  // SUCCESS FALSE WRONG CODE
            "message" => $e->getMessage()
        ];

        return $data;
    }


    $data = [

        "success" => false  // SUCCESS FALSE WRONG CODE

    ];

    return $data;

}


//  STORE USER INFORMATION
$app->post('/add_new_user', function ($request, $response, $args) {

    $resp               =   'error';
    $user_email         =   $request->getParam('user_email');
    $user_password      =   $request->getParam('user_password');


    //CHECK IF USER ALREADY EXIST, IF NO CREATE USER
    $getUser = DB::queryFirstRow("select id,smooch_id from users where smooch_id = '" . $user_email . "'");

    if (DB::count() == 0) {


        $verification_code  =  rand(1000,5000);
        $verification_code  =  md5($verification_code);


        // USER NOT EXIST IN DATABASE, SO CREATE USER IN DATABASE
        DB::insert('users', array(

            'smooch_id' => $user_email,
            "user_name" => $user_email,
            "password"  => $user_password,
            "name" => '',
            "login_verification_hash" => $verification_code
        ));

        sendVerificationEmail($verification_code,$user_email);

        ob_end_clean();

        $resp = 'success';

    }
    else{

        if($getUser['login_verification_hash'] != "success")
        {

            $resp  = "verification_pending";

        }
        else{

            $resp = "account_exist";
        }


    }


    // RESPONSE RETURN TO REST API CALL
    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($resp));
    return $response;

});


// RESEND EMAIL FOR SIGNUP URL

$app->post('/resend_signup_email', function ($request, $response, $args) {


    $user_email   =   $request->getParam('user_email');
    $resp = '';

    //CHECK IF USER ALREADY EXIST, IF NO CREATE USER
    $getUser = DB::queryFirstRow("select * from users where smooch_id = '" . $user_email . "'");


    if(DB::count() != 0) {

        sendVerificationEmail($getUser['login_verification_hash'], $user_email);
        $resp = 'success';
    }
    else{

        $resp = 'error';
    }

    ob_end_clean();


    // RESPONSE RETURN TO REST API CALL
    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($resp));
    return $response;

});



// RESEND EMAIL FOR SIGNUP URL

$app->post('/user_login', function ($request, $response, $args) {

    $resp = "error";

    $user_email   =   $request->getParam('user_email');
    $user_password      =   $request->getParam('user_password');

    //CHECK IF USER ALREADY EXIST, IF NO CREATE USER
    $getUser = DB::queryFirstRow("select * from users where smooch_id = '$user_email' AND password = '$user_password'");

    if (DB::count() != 0) {


        if($getUser['login_verification_hash'] == 'success')
        {
            $resp = 'success';
        }
        else{

            $resp = 'validation_error';

        }

    }

    // RESPONSE RETURN TO REST API CALL
    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($resp));
    return $response;

});




$app->post('/reset_password', function ($request, $response, $args) {

    $user_email   =   $request->getParam('user_email');
    $resp = '';

    //CHECK IF USER ALREADY EXIST, IF NO CREATE USER
    $getUser = DB::queryFirstRow("select * from users where smooch_id = '" . $user_email . "'");

    if(DB::count() != 0) {

        sendPassword($getUser['password'], $user_email);

        ob_end_clean();

        $resp = 'success';
    }
    else{

        $resp = "error";
    }
    // RESPONSE RETURN TO REST API CALL
    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($resp));
    return $response;

});


//  STORE USER ORDER IN DATABASE
$app->post('/add_order', function ($request, $response, $args) {

    try {

        // GET ORDER RESPONSE FROM USER (CLIENT SIDE)
        $user_order = $request->getParam('user_order');
        $user_platform = $request->getParam('user_platform');
        $browser_info = $request->getParam('browser_info');
        $user_id = null;

        //CHECK IF USER ALREADY EXIST, IF NO CREATE USER
        $getUser = DB::queryFirstRow("select id,smooch_id from users where smooch_id = '" . $user_order['email'] . "'");

        if (DB::count() == 0) {

            // USER NOT EXIST IN DATABASE, SO CREATE USER IN DATABASE
            DB::insert('users', array(

                'smooch_id' => $user_order['email'],
                "contact" => $user_order['contact'],
                "address" => $user_order['deliveryAddress'],
                "name" => $user_order['name']
            ));

            $user_id = DB::insertId();
        } else {

            // IF USER ALREADY EXIST IN DATABASE
            $user_id = $getUser['id'];

            DB::update('users', array(

                'smooch_id' => $user_order['email'],
                "contact" => $user_order['contact'],
                "address" => $user_order['deliveryAddress'],
                "name" => $user_order['name']
            ), 'id = %d', $user_id);

        }


        // CHECK IF DISCOUNT GIVEN TO USER ADD IN DB
        $discountType = null;
        $discountValue = 0;

        if ($user_order['isCoupon'] == 'true') {

            if ($user_order['isFixAmountCoupon'] == 'true') {

                $discountType = "fixed value";
            }
            else
            {

                $discountType = "fixed percentage";
            }


            $discountValue = $user_order['discount'];
        }

        $todayDate   =  Date("d/m/Y");
        $db_date     =  Date("Y-m-d");
        $currentTime =  getCurrentTime();


        // CREATE A NEW ORDER AGAINST USER
        DB::insert('user_orders', array(

            'user_id'         => $user_id,
            'restaurant_id'   => $user_order['restaurantId'],
            'total'           => $user_order['total'],
            'coupon_discount' => $discountType,
            'discount_value'  => $discountValue,
            "order_date"      => $db_date." ".$currentTime,
            "platform_info"   => $user_platform,
            'payment_method'  => $user_order['Cash_Card'],
            'transaction_id'  => $user_order['trans_id'],
            'browser_info'    => $browser_info
        ));


        $orderId = DB::insertId();

        foreach ($user_order['cartData'] as $orders) {

            // ADD ORDER DETAIL AGAINST USER
            DB::insert('order_detail', array(

                'order_id' => $orderId,
                'qty' => $orders['qty'],
                'item' => $orders['name'],
                'sub_total' => $orders['price'],
                'sub_items' => $orders['detail']
            ));

        }

        $bot_id = "234472538:AAEwJUUgl0nasYLc3nQtGx4N4bzcqFT-ONs";
        $chat_id = "-165732759";


        telegramAPI($bot_id, $chat_id, createOrderForTelegram($user_order));

        ob_end_clean();


        // SEND EMAIL TO KITCHEN

        email_for_kitchen($user_order, $orderId, $todayDate);

        ob_end_clean();

        email_for_mark($user_order, $orderId, $todayDate);

        ob_end_clean();

        email_for_mark2($user_order, $orderId, $todayDate);

        ob_end_clean();

//
//         CLIENT EMAIL
//         EMAIL ORDER SUMMARY

        if ($user_order['language'] == 'en') {

            email_order_summary_english($user_order, $orderId, $todayDate);

        }
        else {


            email_order_summary_hebrew($user_order, $orderId, $todayDate);

        }


        ob_end_clean();

        // SEND ADMIN COPY EMAIL ORDER SUMMARY

        email_order_summary_hebrew_admin($user_order, $orderId, $todayDate);


        ob_end_clean();



        $delivery_time  = date('H:i:s');

        $delivery_time = strtotime($delivery_time) + 60*60;

        $delivery_time = date('H:i:s',$delivery_time);

        createBringgTask($user_order, $todayDate, $delivery_time) ;

        ob_end_clean();



        // RESPONSE RETURN TO REST API CALL
        $response = $response->withStatus(202);
        $response = $response->withJson(json_encode('success'));
        return $response;

    }
    catch(MeekroDBException $e) {

        $response =  $response->withStatus(500);
        $response =  $response->withHeader('Content-Type', 'text/html');
        $response =  $response->write( $e->getMessage());
        return $response;
    }

});


$app->post('/forget_email_to_b2b_users', function ($request, $response, $args) {

    $email          = $request->getParam('email');
    $password       = $request->getParam('password');

    forget_email_to_b2b_users($email,$password);

    ob_end_clean();

    // RESPONSE RETURN TO REST API CALL
    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode('success'));
    return $response;

});

$app->post('/send_email_to_b2b_users', function ($request, $response, $args) {

    $email          = $request->getParam('email');
    $password       = $request->getParam('password');
    $user_name      = $request->getParam('user_name');

    email_to_b2b_users($email,$password,$user_name);

    ob_end_clean();

    // RESPONSE RETURN TO REST API CALL
    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode('success'));
    return $response;

});

$app->post('/tcs_printer', function ($request, $response, $args) {

    $result = 'resp';

    try {

        $result =   TCS_Service_Printer();

    }
    catch (Exception $e)
    {
        // RESPONSE RETURN TO REST API CALL
        $response = $response->withStatus(202);
        $response = $response->withJson(json_encode("error  ".$e->getMessage()));
        return $response;
    }

    // RESPONSE RETURN TO REST API CALL
    $response = $response->withStatus(202);
    $response = $response->withJson($result);
    return $response;


});

function TCS_Service_Printer()
{
    $API_KEY = 'demoapikey';
    $client = new \GuzzleHttp\Client([

        'timeout' => 100, // NEVER FORGET to set a timeout
        'base_uri' => 'https://imprimo.altercodex.com/api/dev/',
        'headers' => [
            'Authorization' => "Bearer $API_KEY"

        ]

    ]);

    $mystring = "מםפק";


    $response = $client->post('devices/356498044821326/requests/', ['json' => [
        'data' =>  $mystring

    ]]);


    return $response->getBody();
}


//  RETURN PAYMENT URL OF GUARD API AGAINST PAYMENT OF USER ORDER
$app->post('/stripe_payment_request', function ($request, $response, $args) {

    try {

        $amount = $request->getParam('amount');
        $creditCardNo = $request->getParam('cc_no');
        $expDate = $request->getParam('exp_date');
        $cvv = $request->getParam('cvv');
        $user_order = $request->getParam('user_order');


        $user_id = 0;

        $getUser = DB::queryFirstRow("select id,smooch_id from users where smooch_id = '".$user_order['email']."'");

        if (DB::count() == 0) {

            // USER NOT EXIST IN DATABASE, SO CREATE USER IN DATABASE
            DB::insert('users', array(
                'smooch_id' => $user_order['email']
            ));

            $user_id = DB::insertId();

        } else {

            // IF USER ALREADY EXIST IN DATABASE
            $user_id = $getUser['id'];
        }

        $result = '';

        if($user_order['language'] == 'en')
        {
            $result = stripePaymentRequest(($amount*100),$user_order,$user_id,$creditCardNo,$expDate,$cvv);
        }
        else{

            $result = stripePaymentRequestHE(($amount*100),$user_order,$user_id,$creditCardNo,$expDate,$cvv);

        }


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


$app->run();


// SUPPORT METHODS
// STRIPE API PAYMENT REQUEST
// AMOUNT DIVIDED BY 100 FROM API

function  stripePaymentRequest($amount,$user_order,$user_id,$creditCardNo,$expDate,$cvv)
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
								<creditType>RegularCredit</creditType>
								<cardNo>'.$creditCardNo.'</cardNo>
								<cvv>'.$cvv.'</cvv>
								<cardExpiration>'.$expDate.'</cardExpiration>
								<validation>AutoComm</validation>
								<numberOfPayments/>
								<customerData>
									<userData1>'.$user_order['email'].'</userData1>
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
									
									<invoiceSubject>'.$user_order['restaurantTitle'].' Order# '.$user_id.'</invoiceSubject>
									
									<invoiceDiscount/>
									
									<invoiceDiscountRate/>
									
									<invoiceItemCode>'.$user_id.'</invoiceItemCode>
									
									<invoiceItemDescription>'.$user_order['restaurantTitle'].' food order from OrderApp</invoiceItemDescription>
									
									<invoiceItemQuantity>1</invoiceItemQuantity>
									
									<invoiceItemPrice>'.$amount.'</invoiceItemPrice>
									
									<invoiceTaxRate/>
									
									<invoiceComments/>
									
									<companyInfo>OrderApp</companyInfo>
									
									<sendMail>1</sendMail>
									
									<mailTo>'.$user_order['email'].'</mailTo>
									
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

            $rest = [

                "response" =>  (string) $xml->response->message[0]

            ];

        }

    }

    return $rest;
}

function forget_email_to_b2b_users($email,$password)
{
    $mailbody = '<html lang="en">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>OrderApp</title>';

    $mailbody .= '</head><body bgcolor="#f7f7f7" style="background: #f7f7f7;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="600" id="templateColumns" style="color: #000; font-size: 16px; line-height: 18px; font-weight: 400; width: 600px; margin: 0 auto; font-family: Arial, Helvetica, sans-serif;">';
    $mailbody .= '<tr><td align="left" valign="top" width="100%" bgcolor="#ff7f00" style="background: #ff7f00; font-size: 18px; line-height: 22px; font-weight: 700;padding: 20px;">';
    $mailbody .= '<a href="https://orderapp.com" ><img style="display: inline-block; vertical-align: middle; margin: 0;" src="https://dev.orderapp.com/admin/img/email-logo.png" alt="orderapp"></a>';
    $mailbody .= '<h1 style="display: inline-block; vertical-align: middle; margin: 0 10px; font-weight: 400; color: #fff;">Welcome to OrderApp!</h1></td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr>';
    $mailbody .= '<td align="left" valign="top" width="100%" bgcolor="#FFFFFF" style="padding: 50px 25px; background: #fff;">';
    $mailbody .= '<p>Hi '.$email.' <br><br>Your Password details are as follows:</p>';
    $mailbody .= '<p><b>Yours Password : </b> '.$password.'</p><br>';
    $mailbody .= '<p>Visit Website : <a style="color: #3b5998; text-decoration: none;" href="#">'.B2BEMAIL.'</a></p>';
    $mailbody .= '</td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr>';
    $mailbody .= '<td align="center" style="padding: 100px 0 20px;">';
    $mailbody .= '<table border="0" cellspacing="0" cellpadding="0">';
    $mailbody .= '<tr><td width="37" style="text-align: center; padding: 0 8px;"><a href="#"><img src="https://dev.orderapp.com/admin/img/fb.png" width="37" height="37" alt="Facebook" border="0" /></a></td>';
    $mailbody .= '<td width="37" style="text-align: center; padding: 0 8px;"><a href="#"><img src="https://dev.orderapp.com/admin/img/tw.png" width="37" height="37" alt="Twitter" border="0" /></a></td>';
    $mailbody .= '<td width="37" style="text-align: center; padding: 0 8px;"><a href="#"> <img src="https://dev.orderapp.com/admin/img/gp.png" width="37" height="37" alt="Facebook" border="0" /></a></td>';
    $mailbody .= '<td width="37" style="text-align: center; padding: 0 8px;"><a href="#"><img src="https://dev.orderapp.com/admin/img/insta.png" width="37" height="37" alt="Twitter" border="0" /></a></td>';
    $mailbody .= '<td width="37" style="text-align: center; padding: 0 8px;"><a href="#"><img src="https://dev.orderapp.com/admin/img/pin.png" width="37" height="37" alt="Facebook" border="0" /></a></td>';
    $mailbody .= '<td width="37" style="text-align: center; padding: 0 8px;"><a href="#"><img src="https://dev.orderapp.com/admin/img/link.png" width="37" height="37" alt="Twitter" border="0" /></a></td>';
    $mailbody .= '</tr>';
    $mailbody .= '</table>';
    $mailbody .= '</td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr>';
    $mailbody .= '<td><p style="margin: 0; text-align: center;"><a style="color: #797979; text-decoration: none;" href="https://orderapp.com/">orderapp.com</a></p></td></tr></table></body></html>';

    $mail = new PHPMailer;

    $mail->CharSet = 'UTF-8';

    $mail->SMTPDebug = 3;                                               // Enable verbose debug output

    $mail->isSMTP();
    $mail->Host = "email-smtp.eu-west-1.amazonaws.com";                 //   Set mailer to use SMTP
    $mail->SMTPAuth = true;                                             //   Enable SMTP authentication
    $mail->Username = "AKIAJZTPZAMJBYRSJ27A";
    $mail->Password = "AujjPinHpYPuio4CYc5LgkBrSRbs++g9sJIjDpS4l2Ky";   //   SMTP password
    $mail->SMTPSecure = 'tls';                                          //   Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;

    //From email address and name
    $mail->From = "order@orderapp.com";
    $mail->FromName = "OrderApp";


    //To address and name
    $mail->addAddress($email);                    //SEND ADMIN EMAIL


    //Address to which recipient will reply
    $mail->addReplyTo("order@orderapp.com", "Reply");


    //Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = "Your Password Reset Request For B2B";
    $mail->Body = $mailbody;
    $mail->AltBody = "OrderApp";

    if (!$mail->send())
    {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
    else
    {
        echo "Message has been sent successfully";
    }

}

//SEND EMAIL TO B2B USERS CREDENTIALS INFO
function email_to_b2b_users($email,$password,$username)
{

    $mailbody = '<html lang="en">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>OrderApp</title>';

    //    <style type="text/css">
    //    </style>
    $mailbody .= '</head><body bgcolor="#f7f7f7" style="background: #f7f7f7;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="600" id="templateColumns" style="color: #000; font-size: 16px; line-height: 18px; font-weight: 400; width: 600px; margin: 0 auto; font-family: Arial, Helvetica, sans-serif;">';
    $mailbody .= '<tr><td align="left" valign="top" width="100%" bgcolor="#ff7f00" style="background: #ff7f00; font-size: 18px; line-height: 22px; font-weight: 700;padding: 20px;">';
    $mailbody .= '<a href="https://orderapp.com" ><img style="display: inline-block; vertical-align: middle; margin: 0;" src="https://dev.orderapp.com/admin/img/email-logo.png" alt="orderapp"></a>';
    $mailbody .= '<h1 style="display: inline-block; vertical-align: middle; margin: 0 10px; font-weight: 400; color: #fff;">Welcome to OrderApp!</h1></td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr>';
    $mailbody .= '<td align="left" valign="top" width="100%" bgcolor="#FFFFFF" style="padding: 50px 25px; background: #fff;">';
    $mailbody .= '<p>Hi '.$username.' <br><br>Your login details are as follows:</p>';
    $mailbody .= '<p><b>Username : </b> '.$username.'</p>';
    $mailbody .= '<p><b>Password : </b> '.$password.'</p><br>';
    $mailbody .= '<p>Visit Website : <a style="color: #3b5998; text-decoration: none;" href="#">'.B2BEMAIL.'</a></p>';
    $mailbody .= '</td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr>';
    $mailbody .= '<td align="center" style="padding: 100px 0 20px;">';
    $mailbody .= '<table border="0" cellspacing="0" cellpadding="0">';
    $mailbody .= '<tr><td width="37" style="text-align: center; padding: 0 8px;"><a href="#"><img src="https://dev.orderapp.com/admin/img/fb.png" width="37" height="37" alt="Facebook" border="0" /></a></td>';
    $mailbody .= '<td width="37" style="text-align: center; padding: 0 8px;"><a href="#"><img src="https://dev.orderapp.com/admin/img/tw.png" width="37" height="37" alt="Twitter" border="0" /></a></td>';
    $mailbody .= '<td width="37" style="text-align: center; padding: 0 8px;"><a href="#"> <img src="https://dev.orderapp.com/admin/img/gp.png" width="37" height="37" alt="Facebook" border="0" /></a></td>';
    $mailbody .= '<td width="37" style="text-align: center; padding: 0 8px;"><a href="#"><img src="https://dev.orderapp.com/admin/img/insta.png" width="37" height="37" alt="Twitter" border="0" /></a></td>';
    $mailbody .= '<td width="37" style="text-align: center; padding: 0 8px;"><a href="#"><img src="https://dev.orderapp.com/admin/img/pin.png" width="37" height="37" alt="Facebook" border="0" /></a></td>';
    $mailbody .= '<td width="37" style="text-align: center; padding: 0 8px;"><a href="#"><img src="https://dev.orderapp.com/admin/img/link.png" width="37" height="37" alt="Twitter" border="0" /></a></td>';
    $mailbody .= '</tr>';
    $mailbody .= '</table>';
    $mailbody .= '</td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr>';
    $mailbody .= '<td><p style="margin: 0; text-align: center;"><a style="color: #797979; text-decoration: none;" href="https://dev.orderapp.com/en/">orderapp.com</a></p></td></tr></table></body></html>';

    $mail = new PHPMailer;

    $mail->CharSet = 'UTF-8';

    $mail->SMTPDebug = 3;                                               // Enable verbose debug output

    $mail->isSMTP();
    $mail->Host = "email-smtp.eu-west-1.amazonaws.com";                 //   Set mailer to use SMTP
    $mail->SMTPAuth = true;                                             //   Enable SMTP authentication
    $mail->Username = "AKIAJZTPZAMJBYRSJ27A";
    $mail->Password = "AujjPinHpYPuio4CYc5LgkBrSRbs++g9sJIjDpS4l2Ky";   //   SMTP password
    $mail->SMTPSecure = 'tls';                                          //   Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;

    //From email address and name
    $mail->From = "order@orderapp.com";
    $mail->FromName = "B2B OrderApp";


    //To address and name
    $mail->addAddress($email);                    //SEND ADMIN EMAIL


    //Address to which recipient will reply
    $mail->addReplyTo("order@orderapp.com", "Reply");


    //Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = "B2B OrderApp Credentials Info";
    $mail->Body = $mailbody;
    $mail->AltBody = "OrderApp";

    if (!$mail->send())
    {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
    else
    {
        echo "Message has been sent successfully";
    }

}




// STRIPE PAYMENT REQUEST HE

function  stripePaymentRequestHE($amount,$user_order,$user_id,$creditCardNo,$expDate,$cvv)
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
							<language>HEB</language>
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
								<cardNo>'.$creditCardNo.'</cardNo>
								<cvv>'.$cvv.'</cvv>
								<cardExpiration>'.$expDate.'</cardExpiration>
								<validation>AutoComm</validation>
								<numberOfPayments/>
								<customerData>
									<userData1>'.$user_order['email'].'</userData1>
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
									
									<invoiceSubject>'.$user_order['restaurantTitle'].' Order# '.$user_id.'</invoiceSubject>
									
									<invoiceDiscount/>
									
									<invoiceDiscountRate/>
									
									<invoiceItemCode>'.$user_id.'</invoiceItemCode>
									
									<invoiceItemDescription>'.$user_order['restaurantTitle'].' food order from OrderApp</invoiceItemDescription>
									
									<invoiceItemQuantity>1</invoiceItemQuantity>
									
									<invoiceItemPrice>'.$amount.'</invoiceItemPrice>
									
									<invoiceTaxRate/>
									
									<invoiceComments/>
									
									<companyInfo>OrderApp</companyInfo>
									
									<sendMail>1</sendMail>
									
									<mailTo>'.$user_order['email'].'</mailTo>
									
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

        $result = mb_convert_encoding( $result, "HTML-ENTITIES", "UTF-8");

        $xml = simplexml_load_string((string) $result);

        if($xml->response->result[0] == 000)
        {
            $rest = [

                "response" => 'success',
                "trans_id" => (string) $xml->response->tranId[0]
            ];

        }
        else{

            $rest = [

                "response" =>  (string) $xml->response->message[0]

            ];

        }

    }

    return $rest;
}




// CLIENT EMAILS


function sendVerificationEmail($code,$email)
{
    $mailbody  = 'https://'.$_SERVER['HTTP_HOST']."/verification.php?key=".$code.'&email='.$email;

    $mail = new PHPMailer;

    $mail->CharSet = 'UTF-8';

    $mail->SMTPDebug = 3;                                               // Enable verbose debug output

    $mail->isSMTP();
    $mail->Host = "email-smtp.eu-west-1.amazonaws.com";                 //   Set mailer to use SMTP
    $mail->SMTPAuth = true;                                             //   Enable SMTP authentication
    $mail->Username = "AKIAJZTPZAMJBYRSJ27A";
    $mail->Password = "AujjPinHpYPuio4CYc5LgkBrSRbs++g9sJIjDpS4l2Ky";   //   SMTP password
    $mail->SMTPSecure = 'tls';                                          //   Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;

    //From email address and name
    $mail->From = "order@orderapp.com";
    $mail->FromName = "OrderApp";


    //To address and name
    $mail->addAddress($email);     // SEND EMAIL TO USER


    //Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = 'OrderApp Verification';
    $mail->Body = $mailbody;
    $mail->AltBody = "OrderApp";

    if (!$mail->send())
    {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
    else
    {
        echo "Message has been sent successfully";
    }


}


function sendPassword($password,$email)
{
    $mailbody  = 'your password is '.$password;

    $mail = new PHPMailer;

    $mail->CharSet = 'UTF-8';

    $mail->SMTPDebug = 3;                                               // Enable verbose debug output

    $mail->isSMTP();
    $mail->Host = "email-smtp.eu-west-1.amazonaws.com";                 //   Set mailer to use SMTP
    $mail->SMTPAuth = true;                                             //   Enable SMTP authentication
    $mail->Username = "AKIAJZTPZAMJBYRSJ27A";
    $mail->Password = "AujjPinHpYPuio4CYc5LgkBrSRbs++g9sJIjDpS4l2Ky";   //   SMTP password
    $mail->SMTPSecure = 'tls';                                          //   Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;

    //From email address and name
    $mail->From = "order@orderapp.com";
    $mail->FromName = "OrderApp";


    //To address and name
    $mail->addAddress($email);     // SEND EMAIL TO USER


    //Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = 'Your Password Reset Request For OrderApp';
    $mail->Body = $mailbody;
    $mail->AltBody = "OrderApp";

    if (!$mail->send())
    {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
    else
    {
        echo "Message has been sent successfully";
    }


}


// EMAIL ORDER SUMMARY ENGLISH VERSION
function email_order_summary_english($user_order,$orderId,$todayDate)
{
    $mailbody  = '<html><head></head>';
    $mailbody .= '<body style="padding: 0; margin: 0" >';
    $mailbody .= '<div style="max-width: 600px; margin: 0 auto; border: 1px solid #D3D3D3; border-radius: 5px " >';
    $mailbody .= '<div style="font-family: Open Sans" src="https://fonts.googleapis.com/css?family=Open+Sans:300">';
    $mailbody .= '<div  style="background-image: url(http://dev.orderapp.com/restapi/images/header.png); background-repeat: no-repeat; background-position: center; background-size: cover;">';
    $mailbody .= '<table style="width: 100%; color: white; padding: 30px" >';
    $mailbody .= '<tr style="font-size: 30px; padding: 10px" >';
    $mailbody .= '<td > <img style="padding-top: 10px; width: 20px" src="http://dev.orderapp.com/restapi/images/bag.png" > Order Summary </td>';
    $mailbody .= '<td style="text-align: right">'.$user_order['total'].' NIS</td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 10px" >';
    $mailbody .= '<td> '.$todayDate.' &nbsp; Order ID # '.$orderId.'</td>';
    $mailbody .= '<td style="text-align: right">'.$user_order['Cash_Card'].'</td>';
    $mailbody .= '</tr>';
    $mailbody .= '</table>';
    $mailbody .= '</div>';


    $mailbody .= '<div  style="padding: 10px 30px 0px 30px;" >';

    foreach($user_order['cartData'] as $t) {

        $mailbody .= '<table style="width: 100%; color:black; padding: 30px 0; border-bottom: 1px solid #D3D3D3" >';

        $mailbody .= '<tr style="font-size: 18px; padding: 10px; font-weight: bold" >';
        // print item name
        $mailbody .= '<td >' . $t['name'] . '  </td>';
        $mailbody .= '<td style="text-align: right; white-space: nowrap"> '.$t['price'].' NIS X '.$t['qty'].'  &nbsp; <span style="color: FF864C;" >'.(($t['price'] * $t['qty'])).' NIS</span></td>';
        $mailbody .= '</tr>';

        // subitems
        $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';


        if($t['specialRequest'] != "") {

            if ($t['detail'] != '') {

                $mailbody .= '<td >' . $t['detail'] .', Special Request : '.$t['specialRequest']. '</td>';
            }
            else {

                $mailbody .= '<td >' . $t['detail'].' Special Request : '.$t['specialRequest'].' </td>';
            }
        }
        else
        {
            $mailbody .= '<td >' . $t['detail'] . ' </td>';
        }


        $mailbody .= '<td style="text-align: right"> </td>';
        $mailbody .= '</tr>';

        $mailbody .= '</table>';

    }

    if($user_order['pickFromRestaurant'] == 'false' && $user_order['deliveryCharges'] != null && $user_order['deliveryCharges'] != 0) {

        $mailbody .= '<table style="width: 100%; color:black; padding: 30px 0; border-bottom: 1px solid #D3D3D3" >';

        $mailbody .= '<tr style="font-size: 18px; padding: 10px; font-weight: bold" >';

        // print item name
        $mailbody .= '<td > Delivery Fee </td>';
        $mailbody .= '<td style="text-align: right; white-space: nowrap"><span style="color: #FF864C;" >' . (($user_order['deliveryCharges'])) . ' NIS</span></td>';
        $mailbody .= '</tr>';

        $mailbody .= '</tr>';
        $mailbody .= '</table>';

    }

    $mailbody .= '</div>';


    $mailbody .= '<table style="width: 100%; color:black; padding:10px 30px; background: #FEF2E8; border-bottom: 1px solid #D3D3D3 " >';

    if($user_order['isCoupon'] == "false")
    {
        $mailbody .= '<tr style="font-size: 18px;  font-weight: bold" >';
        $mailbody .= '<td style="padding: 5px 0" > Total </td>';
        $mailbody .= '<td style="text-align: right; white-space: nowrap"> <span style="color: #FF864C;" >'.$user_order['total'].' NIS</span></td>';
        $mailbody .= '</tr>';
    }
    else
    {
        $mailbody .= '<tr style="font-size: 18px;  font-weight: bold" >';
        $mailbody .= '<td style="padding: 5px 0" > Sub total  </td>';
        $mailbody .= '<td style="text-align: right; white-space: nowrap"> <span style="color: #FF864C;" >'.$user_order['totalWithoutDiscount'].' NIS</span></td>';
        $mailbody .= '</tr>';

        if($user_order['isFixAmountCoupon'] == 'false')
        {
            $amountDiscount = (($user_order['totalWithoutDiscount'] * $user_order['discount']) / 100);

            $mailbody .= '<tr style="font-size: 18px;  font-weight: bold" >';
            $mailbody .= '<td style="padding: 5px 0" > Coupon Discount -'.$user_order['discount'].'% </td>';
            $mailbody .= '<td style="text-align: right; white-space: nowrap"> <span style="color: #FF864C;" >'.$amountDiscount.' NIS</span></td>';
            $mailbody .= '</tr>';
        }
        else
        {
            $mailbody .= '<tr style="font-size: 18px;  font-weight: bold" >';
            $mailbody .= '<td style="padding: 5px 0" > Coupon Discount Amount </td>';
            $mailbody .= '<td style="text-align: right; white-space: nowrap"> <span style="color: #FF864C;" > -'.$user_order['discount'].' NIS</span></td>';
            $mailbody .= '</tr>';

        }

    }


    $mailbody .= '</table>';


    if($user_order['specialRequest'] != '')
    {

        $mailbody .= '<br><span style="color: #000000; padding:10px 30px;">Special Request : <span style="color: #808080">'.$user_order["specialRequest"].'</span></span><br>';

    }


    $mailbody .= '<table style=" color:black; padding:10px 30px; width: 270px; " cellspacing="5px">';
    $mailbody .= '<tr style="font-size: 18px;  font-weight: bold" >';
    $mailbody .= '<td colspan="2" style="padding: 10px 0" > Customer information   </td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
    $mailbody .= '<td style="padding: 10px 0" > <img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_user.png" ></td>';
    $mailbody .= '<td style="text-align: left; white-space: nowrap"> '.$user_order['name'].' </td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
    $mailbody .= '<td style="padding: 10px 0" > <img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_phone.png" ></td>';
    $mailbody .= '<td style="text-align: left; white-space: nowrap"> '.$user_order['contact'].' </td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
    $mailbody .= '<td style="padding: 10px 0; text-align: center" > <img style=" height: 24px" src="http://dev.orderapp.com/restapi/images/ic_location.png" ></td>';

    if($user_order['pickFromRestaurant'] == 'false')
    {
        $mailbody .= '<td style="text-align: left; white-space: nowrap"> Delivery Address : '.$user_order['deliveryAptNo'].'  '.$user_order['deliveryAddress'].' ('.$user_order['deliveryArea'].')'.'</td>';
    }
    else{

        $mailbody .= '<td style="text-align: left; white-space: nowrap"> Pick From Restaurant : '.$user_order['restaurantAddress'].'</td>';
    }


    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
    $mailbody .= '<td style="padding: 10px 0" > <img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_email.png" ></td>';
    $mailbody .= '<td style="text-align: left; white-space: nowrap"> '.$user_order['email'].' </td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
    $mailbody .= '<td style="padding: 10px 0" > <img style=" width: 20px" src="http://dev.orderapp.com/restapi/images/ic_card.png" ></td>';
    $mailbody .= '<td style="text-align: left; white-space: nowrap"> '.$user_order['Cash_Card'].' </td>';
    $mailbody .= '</tr>';
    $mailbody .= '</table>';

    $mailbody .= '</div>';
    $mailbody .= '</div>';
    $mailbody .= '</body>';
    $mailbody .= '</html>';

    $mail = new PHPMailer;

    $mail->CharSet = 'UTF-8';

    $mail->SMTPDebug = 3;                                               // Enable verbose debug output

    $mail->isSMTP();
    $mail->Host = "email-smtp.eu-west-1.amazonaws.com";                 //   Set mailer to use SMTP
    $mail->SMTPAuth = true;                                             //   Enable SMTP authentication
    $mail->Username = "AKIAJZTPZAMJBYRSJ27A";
    $mail->Password = "AujjPinHpYPuio4CYc5LgkBrSRbs++g9sJIjDpS4l2Ky";   //   SMTP password
    $mail->SMTPSecure = 'tls';                                          //   Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;

//From email address and name
    $mail->From = "order@orderapp.com";
    $mail->FromName = "OrderApp";


//To address and name
    $mail->addAddress($user_order['email']);     // SEND EMAIL TO USER

    $mail->AddCC(EMAIL);                        //SEND  CLIENT EMAIL COPY TO ADMIN

//Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = $user_order['restaurantTitle'].' Order# '.$orderId;
    $mail->Body = $mailbody;
    $mail->AltBody = "OrderApp";

    if (!$mail->send())
    {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
    else
    {
        echo "Message has been sent successfully";
    }


}


// EMAIL ORDER SUMMARY HEBREW VERSION

function email_order_summary_hebrew($user_order,$orderId,$todayDate)
{

    $mailbody  = '<html><head><meta charset="UTF-8"></head>';
    $mailbody  .= '<body style="padding: 0; margin: 0" >';
    $mailbody  .= '<div style="max-width: 600px; margin: 0 auto; border: 1px solid #D3D3D3; border-radius: 5px; overflow: hidden; ">';
    $mailbody  .= '<style>';
    $mailbody  .= '@import url("https://fonts.googleapis.com/css?family=Open+Sans:300");';
    $mailbody  .= '</style>';
    $mailbody  .= '<div style="font-family: Open Sans">';
    $mailbody  .= '<div style="background-image: url(http://dev.orderapp.com/restapi/images/header.png); background-repeat: no-repeat; background-position: center; background-size: cover;" >';
    $mailbody  .= '<table style="width: 100%; color: white; padding: 30px">';
    $mailbody  .= '<tr style="font-size: 30px; padding: 10px">';
    $mailbody  .= '<td dir="rtl" style="text-align: left">'.$user_order['total'].' ש"ח'.'</td>';
    $mailbody  .= '<td style="text-align: right;" >  סיכום הזמנה <img style="padding-top: 10px; width: 20px" src="http://dev.orderapp.com/restapi/images/bag.png" ></td>';
    $mailbody  .= '</tr>';
    $mailbody  .= '<tr style="font-size: 12px; padding: 10px" >';
    $mailbody  .= '<td>'.$user_order['Cash_Card_he'].'</td>';
    $mailbody  .= '<td style="text-align: right" dir="rtl">';
    $mailbody  .=  '&nbsp;'.$todayDate.'&nbsp;';
    $mailbody  .= 'מספר הזמנה #';
    $mailbody  .=  $orderId;
    $mailbody  .= '</tr>';
    $mailbody  .= '</table>';
    $mailbody  .= '</div>';
    $mailbody  .= '<div  style="padding: 10px 30px 0px 30px;" >';


    foreach($user_order['cartData'] as $t) {

        $mailbody.='<table style="width: 100%; color:black; padding: 30px 0; border-bottom: 1px solid #D3D3D3" >';
        $mailbody.='<tr style="font-size: 18px; padding: 10px; font-weight: bold" >';
        $mailbody.='<span style="color: #FF864C;" dir="rtl">';
        $mailbody.=(($t['price'] * $t['qty'])).'  ש"ח ';
        $mailbody.='</span> &nbsp; <span dir="rtl">ש"ח</span>';
        $mailbody.=$t['price'].' x '.$t['qty'].'</td>';
        $mailbody.='<td style="text-align: right;width: 60%" >'. $t['name_he'] .'</td>';
        $mailbody.='</tr>';
        $mailbody.='<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
        $mailbody.='<td > </td>';



        if($t['specialRequest'] != "") {

            if ($t['detail_he'] == '') {


                $mailbody.='<td style="text-align: right; padding: 5px" dir="rtl">'.$t['detail_he'].' הערות : '.$t['specialRequest'].'</td>';

            }
            else {

                $mailbody.='<td style="text-align: right; padding: 5px" dir="rtl">'.$t['detail_he'].', הערות : '.$t['specialRequest'].'</td>';

            }
        }
        else
        {
            $mailbody.='<td style="text-align: right; padding: 5px" dir="rtl">'.$t['detail_he'].'</td>';

        }





        $mailbody.='</tr>';
        $mailbody.='</table>';
    }


    $mailbody .=  '</div>';


    $mailbody .= '<table style="width: 100%; color:black; padding:10px 30px; background: #FEF2E8; border-bottom: 1px solid #D3D3D3 ">';

    if($user_order['isCoupon'] == "false")
    {

        $mailbody .= '<tr style="font-size: 18px;  font-weight: bold">';
        $mailbody .= '<td style=" white-space: nowrap"> <span style="color: #FF864C;" dir="rtl">'.$user_order['total'].' ש"ח '.'</span></td>';
        $mailbody .= '<td style="padding: 5px 0; text-align: right; " > סה"כ </td>';
        $mailbody .= '</tr>';

    }
    else
    {
        $mailbody .= '<tr style="font-size: 18px;  font-weight: bold">';
        $mailbody .= '<td style=" white-space: nowrap"> <span style="color: #FF864C;" >'.$user_order['totalWithoutDiscount'].' ש"ח '.'</span></td>';
        $mailbody .= '<td style="padding: 5px 0; text-align: right; " > סיכום ביניים </td>';
        $mailbody .= '</tr>';


    }

    $mailbody .= '</table>';


    if($user_order['specialRequest'] != '')
    {

        $mailbody .= '<br><span style="color: #000000;text-align: right;float: right;" dir="rtl"> <span style="color: #808080; padding:10px 30px;">בקשה מיוחדת :</span>'.$user_order["specialRequest"].'</span><br>';

    }


    $mailbody .= '<table style="float: right;color:black; padding:10px 30px; width: 270px; position: relative; left: calc(100% - 270px)" cellspacing="5px">';
    $mailbody .= '<tr style="font-size: 18px;  font-weight: bold" >';
    $mailbody .= '<td colspan="2" style="padding: 10px 0; text-align: right" dir="rtl" > מידע ללקוחות   </td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';
    $mailbody .= '<td style="text-align: right; white-space: nowrap"> '.$user_order['name'].' </td>';
    $mailbody .= '<td style="padding: 10px 0"><img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_user.png"></td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';
    $mailbody .= '<td style="text-align: right; white-space: nowrap"> '.$user_order['contact'].' </td>';
    $mailbody .= '<td style="padding: 10px 0"><img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_phone.png"></td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';

    if($user_order['pickFromRestaurant'] == 'false')
    {
        $mailbody .= '<td style="text-align: right; white-space: nowrap" dir="rtl"> כתובת למשלוח : '.$user_order['deliveryAptNo'].'  '.$user_order['deliveryAddress'].' )'.$user_order['deliveryArea'].')</td>';
    }
    else
    {
        $mailbody .= '<td style="text-align: right; white-space: nowrap"dir="rtl">  איסוף עצמי ממסעדה : '.$user_order['restaurantAddress'].'</td>';
    }

    $mailbody .=  '<td style="padding: 10px 0; text-align: center"> <img style="height: 24px" src="http://dev.orderapp.com/restapi/images/ic_location.png" ></td>';
    $mailbody .=  '</tr>';
    $mailbody .=  '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';
    $mailbody .=  '<td style="text-align: right; white-space: nowrap">'.$user_order['email'].'</td>';
    $mailbody .=  '<td style="padding: 10px 0;"><img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_email.png" ></td>';
    $mailbody .=  '</tr>';
    $mailbody .=  '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';
    $mailbody .=  '<td style="text-align: right; white-space: nowrap">'.$user_order['Cash_Card_he'].'</td>';
    $mailbody .=  '<td style="padding: 10px 0;" > <img style=" width: 20px" src="http://dev.orderapp.com/restapi/images/ic_card.png" ></td>';
    $mailbody .=  '</tr>';
    $mailbody .=  '</table>';

    $mailbody .=  '</div></div></body></html>';




    $mail = new PHPMailer;

    $mail->CharSet = 'UTF-8';

    $mail->SMTPDebug = 3;                                               // Enable verbose debug output

    $mail->isSMTP();
    $mail->Host = "email-smtp.eu-west-1.amazonaws.com";                 //   Set mailer to use SMTP
    $mail->SMTPAuth = true;                                             //   Enable SMTP authentication
    $mail->Username = "AKIAJZTPZAMJBYRSJ27A";
    $mail->Password = "AujjPinHpYPuio4CYc5LgkBrSRbs++g9sJIjDpS4l2Ky";   //   SMTP password
    $mail->SMTPSecure = 'tls';                                          //   Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;

    //From email address and name
    $mail->From = "order@orderapp.com";
    $mail->FromName = "OrderApp";


    //To address and name
    $mail->addAddress($user_order['email']);     // SEND EMAIL TO USER
    $mail->addAddress(EMAIL);                    //SEND  CLIENT EMAIL COPY TO ADMIN

    //Address to which recipient will reply
    $mail->addReplyTo("order@orderapp.com", "Reply");


    //Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = 'הזמנה חדשה '." ".$user_order['restaurantTitleHe'];
    $mail->Body = $mailbody;
    $mail->AltBody = "OrderApp";

    if (!$mail->send())
    {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
    else
    {
        echo "Message has been sent successfully";
    }

}



// ADMIN EMAIL
// EMAIL ORDER SUMMARY HEBREW VERSION FOR ADMIN
function email_order_summary_hebrew_admin($user_order,$orderId,$todayDate)
{

    $mailbody  = '<html><head><meta charset="UTF-8"></head>';
    $mailbody  .= '<body style="padding: 0; margin: 0" >';
    $mailbody  .= '<div style="max-width: 600px; margin: 0 auto; border: 1px solid #D3D3D3; border-radius: 5px; overflow: hidden; ">';
    $mailbody  .= '<style>';
    $mailbody  .= '@import url("https://fonts.googleapis.com/css?family=Open+Sans:300");';
    $mailbody  .= '</style>';
    $mailbody  .= '<div style="font-family: Open Sans">';
    $mailbody  .= '<div style="background-image: url(http://dev.orderapp.com/restapi/images/header.png); background-repeat: no-repeat; background-position: center; background-size: cover;" >';
    $mailbody  .= '<table style="width: 100%; color: white; padding: 30px">';
    $mailbody  .= '<tr style="font-size: 30px; padding: 10px">';
    $mailbody  .= '<td dir="rtl" style="text-align: left">'.$user_order['total'].' ש"ח'.'</td>';
    $mailbody  .= '<td style="text-align: right;" >  סיכום הזמנה <img style="padding-top: 10px; width: 20px" src="http://dev.orderapp.com/restapi/images/bag.png" ></td>';
    $mailbody  .= '</tr>';
    $mailbody  .= '<tr style="font-size: 12px; padding: 10px" >';
    $mailbody  .= '<td>'.$user_order['Cash_Card_he'].'</td>';
    $mailbody  .= '<td style="text-align: right" dir="rtl">';
    $mailbody  .=  '&nbsp;'.$todayDate.'&nbsp;';
    $mailbody  .= 'מספר הזמנה #';
    $mailbody  .=  $orderId;
    $mailbody  .= '</tr>';
    $mailbody  .= '</table>';
    $mailbody  .= '</div>';
    $mailbody  .= '<div  style="padding: 10px 30px 0px 30px;" >';


    foreach($user_order['cartData'] as $t) {

        $mailbody.='<table style="width: 100%; color:black; padding: 30px 0; border-bottom: 1px solid #D3D3D3" >';
        $mailbody.='<tr style="font-size: 18px; padding: 10px; font-weight: bold" >';
        $mailbody.='<span style="color: #FF864C;" dir="rtl">';
        $mailbody.=(($t['price'] * $t['qty'])).'  ש"ח ';
        $mailbody.='</span> &nbsp; <span dir="rtl">ש"ח</span>';
        $mailbody.=$t['price'].' x '.$t['qty'].'</td>';
        $mailbody.='<td style="text-align: right;width: 60%" >'. $t['name_he'] .'</td>';
        $mailbody.='</tr>';
        $mailbody.='<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
        $mailbody.='<td > </td>';



        if($t['specialRequest'] != "") {

            if ($t['detail_he'] == '') {


                $mailbody.='<td style="text-align: right; padding: 5px" dir="rtl">'.$t['detail_he'].' הערות : '.$t['specialRequest'].'</td>';

            }
            else {

                $mailbody.='<td style="text-align: right; padding: 5px" dir="rtl">'.$t['detail_he'].', הערות : '.$t['specialRequest'].'</td>';

            }
        }
        else
        {
            $mailbody.='<td style="text-align: right; padding: 5px" dir="rtl">'.$t['detail_he'].'</td>';

        }





        $mailbody.='</tr>';
        $mailbody.='</table>';
    }


    $mailbody .=  '</div>';

    if($user_order['specialRequest'] != '')
    {

        $mailbody .= '<br><span style="color: #000000;text-align: right;float: right;" dir="rtl"> <span style="color: #808080; padding:10px 30px;">בקשה מיוחדת :</span>'.$user_order["specialRequest"].'</span><br>';

    }

    $mailbody .= '<table style="width: 100%; color:black; padding:10px 30px; background: #FEF2E8; border-bottom: 1px solid #D3D3D3 ">';

    if($user_order['isCoupon'] == "false")
    {

        $mailbody .= '<tr style="font-size: 18px;  font-weight: bold">';
        $mailbody .= '<td style=" white-space: nowrap"> <span style="color: #FF864C;" dir="rtl">'.$user_order['total'].' ש"ח '.'</span></td>';
        $mailbody .= '<td style="padding: 5px 0; text-align: right; " > סה"כ </td>';
        $mailbody .= '</tr>';

    }
    else
    {
        $mailbody .= '<tr style="font-size: 18px;  font-weight: bold">';
        $mailbody .= '<td style=" white-space: nowrap"> <span style="color: #FF864C;" >'.$user_order['totalWithoutDiscount'].' ש"ח '.'</span></td>';
        $mailbody .= '<td style="padding: 5px 0; text-align: right; " > סיכום ביניים </td>';
        $mailbody .= '</tr>';


    }

    $mailbody .= '</table>';

    $mailbody .= '<table style="float: right;color:black; padding:10px 30px; width: 270px; position: relative; left: calc(100% - 270px)" cellspacing="5px">';
    $mailbody .= '<tr style="font-size: 18px;  font-weight: bold" >';
    $mailbody .= '<td colspan="2" style="padding: 10px 0; text-align: right" dir="rtl" > מידע ללקוחות   </td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';
    $mailbody .= '<td style="text-align: right; white-space: nowrap"> '.$user_order['name'].' </td>';
    $mailbody .= '<td style="padding: 10px 0"><img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_user.png"></td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';
    $mailbody .= '<td style="text-align: right; white-space: nowrap"> '.$user_order['contact'].' </td>';
    $mailbody .= '<td style="padding: 10px 0"><img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_phone.png"></td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';

    if($user_order['pickFromRestaurant'] == 'false')
    {
        $mailbody .= '<td style="text-align: right; white-space: nowrap" dir="rtl"> כתובת למשלוח : '.$user_order['deliveryAptNo'].'  '.$user_order['deliveryAddress'].' ('.$user_order['deliveryArea'].')</td>';
    }
    else
    {
        $mailbody .= '<td style="text-align: right; white-space: nowrap"dir="rtl">  איסוף עצמי ממסעדה : '.$user_order['restaurantAddress'].'</td>';
    }

    $mailbody .=  '<td style="padding: 10px 0; text-align: center"> <img style="height: 24px" src="http://dev.orderapp.com/restapi/images/ic_location.png" ></td>';
    $mailbody .=  '</tr>';
    $mailbody .=  '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';
    $mailbody .=  '<td style="text-align: right; white-space: nowrap">'.$user_order['email'].'</td>';
    $mailbody .=  '<td style="padding: 10px 0;"><img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_email.png" ></td>';
    $mailbody .=  '</tr>';
    $mailbody .=  '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';
    $mailbody .=  '<td style="text-align: right; white-space: nowrap">'.$user_order['Cash_Card_he'].'</td>';
    $mailbody .=  '<td style="padding: 10px 0;" > <img style=" width: 20px" src="http://dev.orderapp.com/restapi/images/ic_card.png" ></td>';
    $mailbody .=  '</tr>';
    $mailbody .=  '</table>';

    $mailbody .=  '</div></div></body></html>';


    $mail = new PHPMailer;

    $mail->CharSet = 'UTF-8';

    $mail->SMTPDebug = 3;                                               // Enable verbose debug output

    $mail->isSMTP();
    $mail->Host = "email-smtp.eu-west-1.amazonaws.com";                 //   Set mailer to use SMTP
    $mail->SMTPAuth = true;                                             //   Enable SMTP authentication
    $mail->Username = "AKIAJZTPZAMJBYRSJ27A";
    $mail->Password = "AujjPinHpYPuio4CYc5LgkBrSRbs++g9sJIjDpS4l2Ky";   //   SMTP password
    $mail->SMTPSecure = 'tls';                                          //   Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;

    //From email address and name
    $mail->From = "order@orderapp.com";
    $mail->FromName = "OrderApp";


    //To address and name
    $mail->addAddress(EMAIL);                    //SEND ADMIN EMAIL


    //Address to which recipient will reply
    $mail->addReplyTo("order@orderapp.com", "Reply");


    //Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = $user_order['restaurantTitleHe']." הזמנה חדשה # "."  ".$orderId;
    $mail->Body = $mailbody;
    $mail->AltBody = "OrderApp";

    if (!$mail->send())
    {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
    else
    {
        echo "Message has been sent successfully";
    }

}




// ADMIN EMAIL
// EMAIL ORDER SUMMARY HEBREW VERSION FOR ADMIN
function email_for_kitchen($user_order,$orderId,$todayDate)
{

    $mailbody = '<html>
                  <head>
                  <meta charset="UTF-8">
                  </head>
                  <body style="padding: 15px; margin: 15px;font-size: 16px" dir="rtl" >';

    $mailbody .= ' <span dir="rtl">
        שם הלקוח :  
        ' . $user_order['name'] . '
    </span>';

    $mailbody .= '<br>';

    $mailbody .= ' <span dir="rtl">
       מספר :  
  ' . $user_order['contact'] . '
    </span>';

    $mailbody .= '<br>';


    if ($user_order['pickFromRestaurant'] == 'false') {
        $mailbody .= ' <span dir="rtl">
       כתובת:  
    '. $user_order['deliveryAptNo'] .' '. $user_order['deliveryAddress'] .' ('.$user_order['deliveryArea'].')'.'
    </span>';

    }
    else
    {
        $mailbody .= ' <span dir="rtl">
       כתובת:  
    איסוף עצמי
    </span>';
    }

    $mailbody .= '<br>';

    $mailbody .= ' <span dir="rtl">
      הזמנה:  
   ' . substr($user_order['contact'], -4) . '
    </span>';



    if($user_order['specialRequest'] != '')
    {

        $mailbody .= '<br>';

        $mailbody .= ' <span dir="rtl">
      ההערות :  
   ' . $user_order["specialRequest"]  . '
    </span>';


    }

    $mailbody .= '<br>';
    $mailbody .= '<br>';
    $mailbody .= '<br>';

    foreach ($user_order['cartData'] as $t) {


        $mailbody .= '<span dir="rtl">' . $t['qty'] . '  ' . $t['name_he'] . '</span>';
        $mailbody .= '<br>';



        if($t['specialRequest'] != "") {

            if ($t['detail_he'] == '') {


                $mailbody .= '<span dir="rtl">' . preg_replace("/\([^)]+\)/", "", $t['detail_he']).' הערות : '.$t['specialRequest'].'</span>';


            }
            else {


                $mailbody .= '<span dir="rtl">' . preg_replace("/\([^)]+\)/", "", $t['detail_he']).', הערות : '.$t['specialRequest'].'</span>';


            }
        }
        else
        {
            $mailbody .= '<span dir="rtl">' . preg_replace("/\([^)]+\)/", "", $t['detail_he']) . '</span>';

        }






        $mailbody .= '<br>';
        $mailbody .= '<br>';

    }

    $mail = new PHPMailer;

    $mail->CharSet = 'UTF-8';

    $mail->SMTPDebug = 3;                                               // Enable verbose debug output

    $mail->isSMTP();
    $mail->Host = "email-smtp.eu-west-1.amazonaws.com";                 //   Set mailer to use SMTP
    $mail->SMTPAuth = true;                                             //   Enable SMTP authentication
    $mail->Username = "AKIAJZTPZAMJBYRSJ27A";
    $mail->Password = "AujjPinHpYPuio4CYc5LgkBrSRbs++g9sJIjDpS4l2Ky";   //   SMTP password
    $mail->SMTPSecure = 'tls';                                          //   Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;

    //From email address and name
    $mail->From = "order@orderapp.com";
    $mail->FromName = "OrderApp";


    //To address and name
    $mail->addAddress(EMAIL);                    //SEND ADMIN EMAIL


    //Address to which recipient will reply
    $mail->addReplyTo("order@orderapp.com", "Reply");


    //Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = " הזמנה חדשה ".substr($user_order['contact'], -4) . " #" . $user_order['restaurantTitleHe'];
    $mail->Body = $mailbody;
    $mail->AltBody = "OrderApp";

    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
        echo "Message has been sent successfully";
    }

}




function email_for_mark($user_order,$orderId,$todayDate)
{

    $mailbody = '';

    $mailbody .= 'שם הלקוח :'. $user_order['name'];
    $mailbody .= '\n';

    $mailbody .= 'מספר : '.$user_order['contact'];
    $mailbody .= '\n';

    if ($user_order['pickFromRestaurant'] == 'false') {
        $mailbody .= 'כתובת:'. $user_order['deliveryAptNo'] .' '. $user_order['deliveryAddress'] .' ('.$user_order['deliveryArea'].')';
    }
    else
    {
        $mailbody .= 'כתובת: איסוף עצמי';
    }

    $mailbody .= '\n';

    $mailbody .= 'הזמנה:' . substr($user_order['contact'], -4);

    if($user_order['specialRequest'] != '')
    {
        $mailbody .= '\n';

        $mailbody .= 'ההערות : ' . $user_order["specialRequest"];
    }

    $mailbody .= '\n';
    $mailbody .= '\n';
    $mailbody .= '\n';

    foreach ($user_order['cartData'] as $t) {


        $mailbody .= $t['qty'] . '  ' . $t['name_he'] . '  ' . $t['price'];
        $mailbody .= '\n';



        if($t['specialRequest'] != "") {

            if ($t['detail_he'] == '') {


                $mailbody .= preg_replace("/\([^)]+\)/", "", $t['detail_he']).' הערות : '.$t['specialRequest'];


            }
            else {


                $mailbody .= preg_replace("/\([^)]+\)/", "", $t['detail_he']).', הערות : '.$t['specialRequest'];


            }
        }
        else
        {
            $mailbody .= preg_replace("/\([^)]+\)/", "", $t['detail_he']);

        }


        $mailbody .= $user_order['total'] . 'סה"כ ';
        $mailbody .= '\n';
        $mailbody .= '\n';

    }

    $mail = new PHPMailer;

    $mail->CharSet = 'UTF-8';

    $mail->SMTPDebug = 3;                                               // Enable verbose debug output

    $mail->isSMTP();
    $mail->Host = "email-smtp.eu-west-1.amazonaws.com";                 //   Set mailer to use SMTP
    $mail->SMTPAuth = true;                                             //   Enable SMTP authentication
    $mail->Username = "AKIAJZTPZAMJBYRSJ27A";
    $mail->Password = "AujjPinHpYPuio4CYc5LgkBrSRbs++g9sJIjDpS4l2Ky";   //   SMTP password
    $mail->SMTPSecure = 'tls';                                          //   Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;

    //From email address and name
    $mail->From = "order@orderapp.com";
    $mail->FromName = "OrderApp";


    //To address and name
    $mail->addAddress(EMAIL);                    //SEND ADMIN EMAIL


    //Address to which recipient will reply
    $mail->addReplyTo("order@orderapp.com", "Reply");


    //Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = " הזמנה חדשה ".substr($user_order['contact'], -4) . " #" . $user_order['restaurantTitleHe'];
    $mail->Body = $mailbody;
    $mail->AltBody = "OrderApp";

    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
        echo "Message has been sent successfully";
    }

}






function email_for_mark2($user_order,$orderId,$todayDate)
{

    $mailbody = '';

    $mailbody .= 'Name :'. $user_order['name'];
    $mailbody .= '\n';

    $mailbody .= 'Email :'. $user_order['email'];
    $mailbody .= '\n';

    $mailbody .= 'Contact :'. $user_order['contact'];
    $mailbody .= '\n';

    $mailbody .= 'Restaurant Name :'. $user_order['restaurantTitle'];
    $mailbody .= '\n';

    $mailbody .= 'Payment Method : '.$user_order['Cash_Card'];
    $mailbody .= '\n';

    if ($user_order['pickFromRestaurant'] == 'false') {

        $mailbody .= 'Delivery Charges : '. $user_order['deliveryCharges'];
        $mailbody .= '\n';
        $mailbody .= 'Appt No : '. $user_order['deliveryAptNo'];
        $mailbody .= '\n';
        $mailbody .= ' Address : '. $user_order['deliveryAddress'];
        $mailbody .= '\n';
        $mailbody .=  ' Area : ('.$user_order['deliveryArea'].')';
    }
    else
    {
        $mailbody .= 'Pick Up : Pick From Restaurant';
    }


    if($user_order['isCoupon']) {

        $mailbody .= '\n';
        $mailbody .= 'coupon code : ' . $user_order['couponCode'];
        $mailbody .= '\n';

        if ($user_order['isFixAmountCoupon'] == 'true') {

            $mailbody .= 'Discount : ' . $user_order['discount'] . ' NIS';
        } else {

            $mailbody .= 'Discount : ' . $user_order['discount'] . ' %';
        }


    }


    $mailbody .= '\n';
    $mailbody .= 'Total Without Discount : '.$user_order['totalWithoutDiscount'];
    $mailbody .= '\n';
    $mailbody .= 'Total : '.$user_order['total'];

    $mailbody .= '\n';
    $mailbody .= '\n';


    $mail = new PHPMailer;

    $mail->CharSet = 'UTF-8';

    $mail->SMTPDebug = 3;                                               // Enable verbose debug output

    $mail->isSMTP();
    $mail->Host = "email-smtp.eu-west-1.amazonaws.com";                 //   Set mailer to use SMTP
    $mail->SMTPAuth = true;                                             //   Enable SMTP authentication
    $mail->Username = "AKIAJZTPZAMJBYRSJ27A";
    $mail->Password = "AujjPinHpYPuio4CYc5LgkBrSRbs++g9sJIjDpS4l2Ky";   //   SMTP password
    $mail->SMTPSecure = 'tls';                                          //   Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;

//From email address and name
    $mail->From = "order@orderapp.com";
    $mail->FromName = "OrderApp";


//To address and name
    $mail->addAddress(EMAIL);                    //SEND ADMIN EMAIL


//Address to which recipient will reply
    $mail->addReplyTo("order@orderapp.com", "Reply");


//Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = 'Ledger '.$user_order['restaurantTitle'].' Order# '.$orderId;
    $mail->Body = $mailbody;
    $mail->AltBody = "OrderApp";

    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
    else {
        echo "Message has been sent successfully";
    }

}


// BRING SEND ADDRESS OF DELIVERY


function createBringgTask($user_order, $todayDate, $delivery_time) {

    $url = 'https://admin-api.bringg.com//services/6f15901b/caa8a0ea-0b7a-4bd6-87cb-f07c77d66c48/27e666d2-e7cd-4917-988b-aa109829f0c4/';

    if ($user_order['pickFromRestaurant'] == 'true'){
        return;
    }



    date_default_timezone_set('Asia/Jerusalem');
    $ScheduledAt = print_r($todayDate . 'T' . $delivery_time . ':00z',true);

    $order_text = '';
    $order_text .= $user_order['restaurantTitleHe'] .'\n';

    foreach ($user_order['cartData'] as $t) {
        $order_text .= $t['qty'] . '  ' . $t['name_he'] .'\n';
        $order_text .= preg_replace("/\([^)]+\)/", "", $t['detail_he']).'\n\n';
    }

    $jason = print_r('{
       "company_id": 666,
       "title": "Delivery",      // Title for the Task being created.
       "scheduled_at": "' . $ScheduledAt . '",   // Here the  $ScheduledAt variable is an example for the date and time format.
       "note": "' . $order_text . '",
       "customer": {
          "name": "' . $user_order['name'] . '",
          "address": "' . $user_order['deliveryAddress'] . '",
          "phone": "' . $user_order['contact'] . '"
       },
       "way_points":[
          {
             "customer":{
                "name": "' . $user_order['restaurantTitleHe'] . '",
                "phone": "026222862"
             },
             "address":"'.$user_order['deliveryAddress'].' ('.$user_order['deliveryArea'].')",
             "restaurantAddress":"'.$user_order['restaurantAddress'].'"
            "allow_editing_inventory": "true", // Allow driver to edit the Inventory e.g. change quantities?
            "must_approve_inventory": "true",   // Driver must approve pick up inventory e.g. by scanning it. The driver won\'t be allowed to leave location without doing this.
            "allow_scanning_inventory": "true"  // Allow to scan inventory via phone camera (on the Driver App)
          },
          {
             "customer":{
                "name":"' . $user_order['name'] . '",
                "phone":"' . $user_order['contact'] . '"
             },
             "address":"' . $user_order['deliveryAddress'] . '",
            "allow_editing_inventory": "true",    // Allow driver to edit the Inventory e.g. change quantities?
            "must_approve_inventory": "true",   // Driver must approve drop off inventory e.g. by scanning it. The driver won\'t be allowed to leave location and finish the task without doing this.
            "allow_scanning_inventory": "true" // Allow to scan inventory via phone camera (on the Driver App)
          }
       ]
    }',true);



    $ch=curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jason);
    curl_setopt($ch, CURLOPT_HTTPHEADER,
        array('Content-Type:application/json',
            'Content-Length: ' . strlen($jason))
    );

    $json_response = curl_exec($ch);

    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ( $status != 200 ) {

        //die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));

    }

    curl_close($ch);

}




function createOrderForTelegram($user_order)
{
    $mailBody = '';
    $mailBody = " הזמנה חדשה".substr($user_order['contact'], -4) . " #" . $user_order['restaurantTitleHe'].'
    
    ';

    $mailBody .= 'שם הלקוח :' . $user_order['name'] . '
    
    ';
    $mailBody .= 'מספר :' . $user_order['contact'] . '
    
    ';

    if ($user_order['pickFromRestaurant'] == 'false') {

        $mailBody .= ':  כתובת'. $user_order['deliveryAptNo'] .' '. $user_order['deliveryAddress'] .' ('.$user_order['deliveryArea'].')'.' 
        
        ';
    }
    else
    {
        $mailBody .= 'כתובת: איסוף עצמי'.'
        
        ' ;
    }

    $mailBody .= 'הזמנה :' . substr($user_order['contact'], -4) . ' 
    
    ';


    if($user_order['specialRequest'] != '')
    {

        $mailBody .= 'ההערות :' . $user_order["specialRequest"] . ' 
    
       ';
    }



    foreach ($user_order['cartData'] as $t) {

        $mailBody .=  $t['qty'] . '  ' . $t['name_he'] . ' 
        
        ';

        if($t['specialRequest'] != "") {


            if ($t['detail_he'] == '') {


                $mailBody .=  preg_replace("/\([^)]+\)/", "", $t['detail_he']).'הערות :'.$t['specialRequest'].' 
                
                ';

            }
            else {


                $mailBody .= preg_replace("/\([^)]+\)/", "", $t['detail_he']).' ,הערות :'.$t['specialRequest'].' 
               
                ';

            }
        }
        else
        {
            $mailBody .= preg_replace("/\([^)]+\)/", "", $t['detail_he']) . ' 
            
            ';

        }
    }






    return $mailBody;
}




//  TELEGRAM API
//  SEND ORDERS THROUGH TELEGRAM

function telegramAPI($bot_id, $chat_id, $text) {


    $postData = array(
        'chat_id' => $chat_id,
        'text' => $text
    );


    $headers = array(
        'Content-Type: application/json'
    );


    $url = 'https://api.telegram.org/bot'.$bot_id.'/sendMessage';


    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    $response = curl_exec($ch);
    echo "Response: ".$response;
    curl_close($ch);


}
function getCurrentTime(){

    //CURRENT TIME OF ISRAEL
    date_default_timezone_set("Asia/Jerusalem");
    $currentTime           =    date("H:i:s");

    return $currentTime;
}

?>

