<?php

require      'vendor/autoload.php';
require      'PHPMailer/PHPMailerAutoload.php';
require_once 'inc/initDb.php';


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
        $user     = '';
        $company  = '';

        DB::useDB('orderapp_b2b_wui');

        $userDB = DB::queryFirstRow("select * from b2b_users where user_name = '$user_name' and password = '$password'");


        if (DB::count() > 0)
        {


            $company_id         = $userDB['company_id'];
            $companyDB          = DB::queryFirstRow("select * from company where id = $company_id");


            $user['user_id']                    =   $userDB['id'];
            $user['name']                       =   $userDB['name'];
            $user['email']                      =   $userDB['smooch_id'];
            $user['contact']                    =   $userDB['contact'];
            $user['userDiscountFromCompany']    =   $userDB['discount'];
            $company['company_id']              =   $company_id;
            $company['company_name']            =   $companyDB['name'];
            $company['company_address']         =   $companyDB['delivery_address'];
            $company['company_discount']        =   $companyDB['discount'];
            $company['discount_type']           =   $companyDB['discount_type'];


            $obj['company']                 =   $company;
            $obj['user']                    =   $user;
            $obj['error']                   =   false;


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



        // CURRENT TIME OF ISRAEL
        date_default_timezone_set("Asia/Jerusalem");
        $currentTime = date("H:i");
        $dayOfWeek   = date('l');


        $today_timings = "";


        foreach ($restaurantTimings as $singleTime) {


            if ($singleTime['week_en'] == $dayOfWeek) {


                if($singleTime['opening_time'] != "Closed") {


                    $today_timings = $singleTime['opening_time'] . " - " . $singleTime['closing_time'];
                    $openingTime = DateTime::createFromFormat('H:i', $singleTime['opening_time']);
                    $closingTime = DateTime::createFromFormat('H:i', $singleTime['closing_time']);
                    $currentTimes = DateTime::createFromFormat('H:i', $currentTime);

                    // ESTIMATE DELIVERY TIME 1 HOUR LATER THEN CLOSING TIME

                    $delivery_time = strtotime($singleTime['closing_time']) + 60*60;
                    $delivery_time = date('H:i', $delivery_time);

                    $delivery_time_end = strtotime($delivery_time) + 60*60;
                    $delivery_time_end = date('H:i', $delivery_time_end);

                    $delivery_time_str = $delivery_time." - ".$delivery_time_end;

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
            "db_kashrut"            => $db_restaurant_kashrut

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


            $results = DB::query(" SELECT * FROM b2b_orders WHERE date BETWEEN CURDATE()-INTERVAL 1 WEEK AND CURDATE() AND user_id = $user_id AND order_status <> 'pending'");

        }
        else{

            $start_date = $request->getParam('start_date');
            $end_date = $request->getParam('end_date');

            $start_date = DateTime::createFromFormat('m/d/Y', $start_date);
            $end_date = DateTime::createFromFormat('m/d/Y', $end_date);

            $start_date = $start_date->format('Y-m-d');
            $end_date = $end_date->format('Y-m-d');


            $results = DB::query(" SELECT * FROM b2b_orders WHERE date BETWEEN '$start_date'  AND  '$end_date' AND user_id = $user_id AND order_status <> 'pending'");
        }


        $ctn = 0;


        foreach ($results as $result)
        {

            DB::useDB('orderapp_restaurants_b2b_wui');

            $restaurant   =  DB::queryFirstRow("select name_en from restaurants where id = '" . $result['restaurant_id'] . "'");

            $results[$ctn]['rest_name'] = $restaurant['name_en'];

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





//  GET ALL PAST ORDERS FOR LAST WEEKS
$app->post('/get_all_pending_orders', function ($request, $response, $args)
{
    try
    {
        DB::useDB('orderapp_b2b_wui');


        $user_id = $request->getParam('user_id');

        $results  =  DB::query("select * from b2b_orders where user_id = $user_id AND order_status = 'pending' ");


        $ctn = 0;


        foreach ($results as $result)
        {

            DB::useDB('orderapp_restaurants_b2b_wui');

            $restaurant   =  DB::queryFirstRow("select * from restaurants where id = '" . $result['restaurant_id'] . "'");

            DB::useDB('orderapp_restaurants_b2b_wui');

            $city = DB::queryFirstRow("select * from cities where id = '" . $restaurant['city_id'] . "'");

            $restaurant['city_name'] = $city['name_en'];

            $results[$ctn]['rest'] = $restaurant;

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




// CANCEL ORDER
$app->post('/get_db_tags_and_kashrut', function ($request, $response, $args)
{
    DB::useDB(B2B_RESTAURANTS);

    $company_id = $request->getParam('company_id');

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



        $resp = [
            "db_tags"               => $db_restaurant_tags,          //
            "db_kashrut"            => $db_restaurant_kashrut

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
    $cgConf['amount']= 0;
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
								<validation>Token</validation>
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

            $data = [

                "success" => false,  // SUCCESS FALSE WRONG CODE
                "error"   => (string) $xml->response->message[0]

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

        $card = DB::queryFirstRow("select * from user_credit_cards where id = $cId");


        if ($order_data['language'] == 'en') {


            $result = stripePaymentRequest(($order_data['total_paid'] * 100), $user_id, $email, $card['card_id'], $card['expiration'], $card['cvv']);


        }
        else {


            $result = stripePaymentRequestHE(($order_data['total_paid'] * 100), $user_id, $email, $card['card_id'], $card['expiration'], $card['cvv']);


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



// SUPPORT METHODS
// STRIPE API PAYMENT REQUEST
// AMOUNT DIVIDED BY 100 FROM API

function  stripePaymentRequest($amount, $user_id, $email ,$creditCardNo, $expDate, $cvv)
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



    $poststring .= '<cardId>'.$creditCardNo.'</cardId>';


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

            $rest = [

                "response" =>  (string) $xml->response->message[0]

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
    $smooch_id  = $user_order['user']['email'];


    date_default_timezone_set("Asia/Jerusalem");
    $todayDate  = Date("d/m/Y");
    $today = date("Y-m-d");



    //CHECK IF USER ALREADY EXIST, IF NO CREATE USER
    $getUser = DB::queryFirstRow("select * from b2b_users where smooch_id = '" . $user_order['user']['email'] . "'");


    $discount = $getUser['discount'] - $user_order['company_contribution'];



    // CREATE A NEW ORDER AGAINST USER
    DB::useDB(B2B_DB);


    DB::insert('b2b_orders', array(

        'user_id'                       => $user_order['user']['user_id'],
        'company_id'                    => $user_order['company']['company_id'],
        'total'                         => $user_order['total_paid'],
        'actual_total'                  => $user_order['actual_total'],
        'discount'                      => $user_order['discount'],
        'company_contribution'          => $user_order['company_contribution'],
        'transaction_id'                => $user_order['transactionId'],
        'restaurant_id'                 => $user_order['rests_orders'][0]['selectedRestaurant']['id'],
        "date"                          => DB::sqleval("NOW()"),
        "rest_order_object"             => json_encode($user_order),
        "payment_info"                  => $user_order['payment_option']
    ));


    $orderId = DB::insertId();


    //GET COMPANY NAME
    DB::useDB(B2B_DB);

    $getCompanyName = DB::queryFirstRow("select * from company where id = '" . $user_order['company']['company_id'] . "'");
    $user_order['company']['discount_type'] = $getCompanyName['discount_type'];



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


//    // EMAIL FOR LEDGER
//
//    email_for_mark2($user_order, $orderId, $todayDate);
//    ob_end_clean();
//
//
//    // SEND ADMIN COPY EMAIL ORDER SUMMARY
//
//    email_order_summary_hebrew_admin($user_order, $orderId, $todayDate);
//    ob_end_clean();
//
//
//    // CLIENT EMAIL
//    // EMAIL ORDER SUMMARY
//    //
//    if ($user_order['language'] == 'en') {
//
//
//        email_order_summary_english($user_order, $orderId, $todayDate);
//
//    }
//    else
//    {
//
//        email_order_summary_hebrew($user_order, $orderId, $todayDate);
//
//    }
//
//    ob_end_clean();




    DB::useDB(B2B_DB);

    DB::query("UPDATE b2b_users SET date = '$today', discount = '$discount'  WHERE  smooch_id = '$smooch_id'");


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

// CANCEL ORDER
$app->post('/cancel_order', function ($request, $response, $args)
{
    DB::useDB(B2B_DB);
    $order_id = $request->getParam('order_id');

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



//GENERATE EMAIL FORGET PASSWORD USER CRENDENTIALS
function mailForgotPassword($password, $username, $user_email){

    $mailbody = '<!DOCTYPE html>';
    $mailbody .= '<html lang="en">';
    $mailbody .= '<head>';
    $mailbody .= '<meta charset="UTF-8">';
    $mailbody .= '<title>Email Template</title>';
    $mailbody .= '<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">';
    $mailbody .= '</head>';
    $mailbody .= '<body style="background:#fff; font-family:\'Open Sans\',sans-serif;">';
    $mailbody .= '<style>';
    $mailbody .= '@import url(https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800);';
    $mailbody .= '</style>';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" align="center" height="100%" width="500" style="text-align: left; overflow:hidden; background-color:#fff; color: #000; border: 1px solid #cacaca; font-size: 15px; line-height: 18px; font-weight: 400;">';
    $mailbody .= '<tr><td align="center" valign="top">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="500">';
    $mailbody .= '<tr><td align="center" valign="top" style="padding: 20px 15px; background:#ff7f00;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="100%" id="emailHeader">';
    $mailbody .= '<tr><td align="center" valign="top">';
    $mailbody .= '<h1 style="text-align: left; margin: 0 0 5px; color: #fff; font-size: 20px; line-height: 23px; font-weight: 700;">Password recovery</h1>';
    $mailbody .= '<p style="text-align: left; margin: 0; color: #fff;">'.date("Y/m/d").'</p></td>';
    $mailbody .= '<td align="center" valign="top" style="text-align: right; width: 52px;"><img style="display: block;" src="https://'.$_SERVER['HTTP_HOST'].'/restapi/images/delivery-email.png"></td></tr>';
    $mailbody .= '<tr><td align="center" valign="top"></td></tr></table></td></tr>';
    $mailbody .= '<tr><td align="center" valign="top" style="padding: 30px 15px 10px;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="100%">';
    $mailbody .= '<tr><td align="center" valign="top" style="text-align: left; width: 100px; font-weight: 400;">';
    $mailbody .= '<p style="margin:0;">Your account is active and you may login from our b2b Ordering page, Account details are given below</p></td></tr></table></td></tr>';
    $mailbody .= '<tr><td align="center" valign="top" style="padding: 10px 15px;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="100%">';
    $mailbody .= '<tr><td align="center" valign="top" style="text-align: left; width: 100px; font-weight: 700;">';
    $mailbody .= 'User Name:';
    $mailbody .= '</td><td align="center" valign="top" style="text-align: left;">';
    $mailbody .= $username;
    $mailbody .= '</td></tr></table></td></tr>';
    $mailbody .= '<tr><td align="center" valign="top" style="padding: 10px 15px 30px;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr>';
    $mailbody .= '<td align="center" valign="top" style="text-align: left; width: 100px; font-weight: 700;">';
    $mailbody .= 'Password:';
    $mailbody .= '</td><td align="center" valign="top" style="text-align: left;">';
    $mailbody .= $password;
    $mailbody .= '</td></tr></table>	';
    $mailbody .= '</td></tr><tr>';
    $mailbody .= '<td align="center" valign="top" style="padding: 10px 15px; background: #ffae5e;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="100%">';
    $mailbody .= '<tr><td align="center" valign="top" style="text-align: left; width: 100px; font-weight: 700;">';
    $mailbody .= '<a style="display: block; width: 87px; margin: 0 auto; color: #fff;" href="'.$_SERVER['HTTP_HOST'].'"><img style="display: block; margin: 0 auto;" src="https://'.$_SERVER['HTTP_HOST'].'/restapi/images/logo-image.png"></a></td></tr></table>';
    $mailbody .= '</td></tr></table></td></tr></table>';
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
    $mail->From = "orders@orderapp.com";
    $mail->FromName = "OrderApp";


    //To address and name
    $mail->addAddress($user_email);                      // SEND EMAIL TO USER

    $mail->AddCC(EMAIL);                        //SEND  CLIENT EMAIL COPY TO ADMIN

    //Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = 'Password Recovery';
    $mail->Body =    $mailbody;
    $mail->AltBody = "OrderApp";


    if ($mail->send())
    {
        $msg = "Message has been sent successfully";
        return false;

    }
    else
    {

        $msg = "Mailer Error: " . $mail->ErrorInfo;
        return true;

    }

}


//SEND REPORT TO DEV TEAM ERRORS
function sendReportToDevTeam($host, $url, $message){

    $mailbody = '<!DOCTYPE html>';
    $mailbody .= '<html lang="en">';
    $mailbody .= '<head>';
    $mailbody .= '<meta charset="UTF-8">';
    $mailbody .= '<title>Email Template</title>';
    $mailbody .= '<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">';
    $mailbody .= '</head>';
    $mailbody .= '<body style="background:#fff; font-family:\'Open Sans\',sans-serif;">';
    $mailbody .= '<style>';
    $mailbody .= '@import url(https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800);';
    $mailbody .= '</style>';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" align="center" height="100%" width="500" style="text-align: left; overflow:hidden; background-color:#fff; color: #000; border: 1px solid #cacaca; font-size: 15px; line-height: 18px; font-weight: 400;">';
    $mailbody .= '<tr><td align="center" valign="top">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="500">';
    $mailbody .= '<tr><td align="center" valign="top" style="padding: 20px 15px; background:#ff7f00;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="100%" id="emailHeader">';
    $mailbody .= '<tr><td align="center" valign="top">';
    $mailbody .= '<h1 style="text-align: left; margin: 0 0 5px; color: #fff; font-size: 20px; line-height: 23px; font-weight: 700;">Password recovery</h1>';
    $mailbody .= '<p style="text-align: left; margin: 0; color: #fff;">'.date("Y/m/d").'</p></td>';
    $mailbody .= '<td align="center" valign="top" style="text-align: right; width: 52px;"><img style="display: block;" src="https://'.$_SERVER['HTTP_HOST'].'/restapi/images/delivery-email.png"></td></tr>';
    $mailbody .= '<tr><td align="center" valign="top"></td></tr></table></td></tr>';
    $mailbody .= '<tr><td align="center" valign="top" style="padding: 30px 15px 10px;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="100%">';
    $mailbody .= '<tr><td align="center" valign="top" style="text-align: left; width: 100px; font-weight: 400;">';
    $mailbody .= '<p style="margin:0;">Error occurs on system, details are given below </p></td></tr></table></td></tr>';
    $mailbody .= '<tr><td align="center" valign="top" style="padding: 10px 15px;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="100%">';
    $mailbody .= '<tr><td align="center" valign="top" style="text-align: left; width: 100px; font-weight: 700;">';
    $mailbody .= 'HOST NAME :';
    $mailbody .= '</td><td align="center" valign="top" style="text-align: left;">';
    $mailbody .= $host;
    $mailbody .= '</td></tr></table></td></tr>';
    $mailbody .= '<tr><td align="center" valign="top" style="padding: 10px 15px 10px;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr>';
    $mailbody .= '<td align="center" valign="top" style="text-align: left; width: 100px; font-weight: 700;">';
    $mailbody .= 'URL :';
    $mailbody .= '</td><td align="center" valign="top" style="text-align: left;">';
    $mailbody .= $url;
    $mailbody .= '</td></tr></table>	';
    $mailbody .= '</td></tr><tr>';

    $mailbody .= '<tr><td align="center" valign="top" style="padding: 10px 15px 30px;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr>';
    $mailbody .= '<td align="center" valign="top" style="text-align: left; width: 100px; font-weight: 700;">';
    $mailbody .= 'MESSAGE :';
    $mailbody .= '</td><td align="center" valign="top" style="text-align: left;">';
    $mailbody .= $message;
    $mailbody .= '</td></tr></table>	';
    $mailbody .= '</td></tr><tr>';

    $mailbody .= '<td align="center" valign="top" style="padding: 10px 15px; background: #ffae5e;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="100%">';
    $mailbody .= '<tr><td align="center" valign="top" style="text-align: left; width: 100px; font-weight: 700;">';
    $mailbody .= '<a style="display: block; width: 87px; margin: 0 auto; color: #fff;" href="'.$_SERVER['HTTP_HOST'].'"><img style="display: block; margin: 0 auto;" src="https://'.$_SERVER['HTTP_HOST'].'/restapi/images/logo-image.png"></a></td></tr></table>';
    $mailbody .= '</td></tr></table></td></tr></table>';
    $mailbody .= '</body>';
    $mailbody .= '</html>';




    $mail = new PHPMailer;
    $mail->CharSet = 'UTF-8';
    $mail->SMTPDebug = 3;                                                 // Enable verbose debug output

    $mail->isSMTP();
    $mail->Host = "email-smtp.eu-west-1.amazonaws.com";                   //   Set mailer to use SMTP

    $mail->SMTPAuth = true;                                               //   Enable SMTP authentication
    $mail->Username = "AKIAJZTPZAMJBYRSJ27A";
    $mail->Password = "AujjPinHpYPuio4CYc5LgkBrSRbs++g9sJIjDpS4l2Ky";     //   SMTP password
    $mail->SMTPSecure = 'tls';                                            //   Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;

    //From email address and name
    $mail->From = "orders@orderapp.com";
    $mail->FromName = "OrderApp";


    //To address and name
    $mail->addAddress('iftikhar@experintsol.com');                // SEND EMAIL TO DEV TEAM

    $mail->AddCC(EMAIL);                                          //SEND  CLIENT EMAIL COPY TO ADMIN

    //Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = 'Password Recovery';
    $mail->Body =    $mailbody;
    $mail->AltBody = "OrderApp";


    if ($mail->send())
    {
        $msg = "Message has been sent successfully";
        return false;

    }
    else
    {

        $msg = "Mailer Error: " . $mail->ErrorInfo;
        return true;

    }

}

// EMAIL ORDER SUMMARY ENGLISH VERSION
function email_order_summary_english($user_order,$orderId,$todayDate)
{

    $mailbody  = '<html><head></head>';
    $mailbody .= '<body style="padding: 0; margin: 0" >';
    $mailbody .= '<div style="max-width: 600px; margin: 0 auto; border: 1px solid #D3D3D3; border-radius: 5px; overflow: hidden " >';
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
        if($t['specialRequest'] != "") {

            if ($t['detail'] != '') {

                $mailbody .= '<td >' . $t['detail'] .', Special Request : '.$t['specialRequest']. '</td>';
            }
            else
            {

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

    }

    //TODAY REMAINING BALANCE SECTION
    $mailbody .= '<tr style="font-size: 18px;  font-weight: bold" >';
    if($user_order['discount_type'] == "daily"){
        $mailbody .= "<td style='padding: 5px 0' >Remaining Balance Today* </td>";
    }
    else{
        $mailbody .= "<td style='padding: 5px 0' >Remaining Balance for the month* </td>";
    }

    $mailbody .= '<td style="text-align: right; white-space: nowrap"> <span style="color: #FF864C;" > '.$user_order['discount'].' NIS</span></td>';
    $mailbody .= '</tr>';



    $mailbody .= '</table>';

    if($user_order['specialRequest'] != '')
    {

        $mailbody .= '<br><span style="color: #000000; padding:10px 30px;">Special Request : <span style="color: #808080">'.$user_order["specialRequest"].'</span></span><br>';

    }


    $mailbody .= '<table style=" color:black; padding:10px 30px; width: 100%; " cellspacing="5px">';
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
    //COMPANY INFO
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
    $mailbody .= '<td style="padding: 10px 0" > <img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_company.png" ></td>';
    $mailbody .= '<td style="text-align: left; white-space: nowrap"> '.$user_order['company_name'].' </td>';
    $mailbody .= '</tr>';
    //COMPANY INFO ENDS

    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
    $mailbody .= '<td style="padding: 10px 0; text-align: center" > <img style=" height: 24px" src="http://dev.orderapp.com/restapi/images/ic_location.png" ></td>';

    $mailbody .= '<td style="text-align: left; white-space: nowrap"> Deliver At : '.$user_order['delivery_address'].'</td>';



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
    $mailbody .= '<h4 style="padding: 5px 27px;">* Use by end of '.$user_order['company_name'].' orderding time</h4>';

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
    $mail->From = "orders@orderapp.com";
    $mail->FromName = "OrderApp";


//To address and name
    $mail->addAddress($user_order['email']);     // SEND EMAIL TO USER
    $mail->AddCC(EMAIL);
    $mail->AddCC("brina@orderapp.com");
    $mail->AddBCC("oded@orderapp.com");


//Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = 'Biz '.$user_order['restaurantTitle'].' Order# '.$orderId;
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
    $mailbody  .= '<div style="max-width: 600px; margin: 0 auto; border: 1px solid #D3D3D3; border-radius: 5px; overflow: hidden ">';
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
        $mailbody.=(($t['price'] * $t['qty'])). ' ש"ח';
        $mailbody.='</span> &nbsp; <span dir="rtl">  ש"ח</span>';
        $mailbody.=$t['price'].' x '.$t['qty'].'</td>';
        $mailbody.='<td style="text-align: right;" >'. $t['name_he'] .'</td>';
        $mailbody.='</tr>';
        $mailbody.='<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
        $mailbody.='<td > </td>';
        $mailbody.='<td style="text-align: right; padding: 5px" dir="rtl">'.$t['detail_he'].'</td>';

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

    $mailbody .= '</div>';
    $mailbody .= '<table style="width: 100%; color:black; padding:10px 30px; background: #FEF2E8; border-bottom: 1px solid #D3D3D3 ">';

    if($user_order['isCoupon'] == "false")
    {

        $mailbody .= '<tr style="font-size: 18px;  font-weight: bold">';
        $mailbody .= '<td style=" white-space: nowrap"> <span style="color: #FF864C;" >&nbsp;<span dir="rtl">ש"ח</span>&nbsp;'.$user_order['total']. '</span></td>';
        $mailbody .= '<td style="padding: 5px 0; text-align: right; " > סה"כ </td>';
        $mailbody .= '</tr>';

    }
    else
    {
        $mailbody .= '<tr style="font-size: 18px;  font-weight: bold">';
        $mailbody .= '<td style=" white-space: nowrap"> <span style="color: #FF864C;" >&nbsp;<span dir="rtl">ש"ח</span>&nbsp;'.$user_order['totalWithoutDiscount'].'</span></td>';
        $mailbody .= '<td style="padding: 5px 0; text-align: right; " > סיכום ביניים </td>';
        $mailbody .= '</tr>';


    }

    //TODAY REMAINING BALANCE SECTION
    $mailbody .= '<tr style="font-size: 18px;  font-weight: bold" >';
    $mailbody .= '<td style="white-space: nowrap"> <span style="color: #FF864C;" >&nbsp;<span dir="rtl">ש"ח</span>&nbsp; '.$user_order['discount'].'</span></td>';
    if($user_order['discount_type'] == "daily"){
        $mailbody .= '<td style="padding: 5px 0; text-align: right;" >יתרת היום* </td>';
    }
    else{
        $mailbody .= '<td style="padding: 5px 0; text-align: right;" >יתרה חודשית* </td>';
    }

    $mailbody .= '</tr>';


    $mailbody .= '</table>';
    if($user_order['specialRequest'] != '')
    {

        $mailbody .= '<br><span style="color: #000000;text-align: right;float: right;" dir="rtl"> <span style="color: #808080; padding:10px 30px;">בקשה מיוחדת :</span>'.$user_order["specialRequest"].'</span><br>';

    }


    $mailbody .= '<table style="float: right;color:black; padding:10px 30px; width: 100%; position: relative; left: calc(100% - 270px)" cellspacing="5px">';
    $mailbody .= '<tr style="font-size: 18px;  font-weight: bold" >';
    $mailbody .= '<td colspan="2" style="padding: 10px 0; text-align: right" dir="rtl" > מידע ללקוחות   </td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';
    $mailbody .= '<td style="text-align: right; white-space: nowrap"> '.$user_order['contact'].' </td>';
    $mailbody .= '<td style="padding: 10px 0"><img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_phone.png"></td>';
    $mailbody .= '</tr>';

    //COMPANY INFO
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';
    $mailbody .= '<td style="text-align: right; white-space: nowrap"> '.$user_order['company_name'].' </td>';
    $mailbody .= '<td style="padding: 10px 0"><img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_company.png"></td>';
    $mailbody .= '</tr>';


    //COMPANY INFO ENDS


    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';

    //COMPANY ADDRESS
    $mailbody .= '<td style="text-align: right; white-space: nowrap" dir="rtl"> לספק ב : '.$user_order['delivery_address'].'</td>';



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
    //COMMENT SECTION
    $mailbody .=  '<tr style="font-size: 13px; padding: 5px 10px; color: #000; font-weight: bold;">';
    $mailbody .=  '<td style="text-align: right;">  יש להשתמש ביתרה עד סוף זמן ההזמנה של בית העסק *</td>';

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
    $mail->From = "orders@orderapp.com";
    $mail->FromName = "OrderApp";


    //To address and name
    $mail->addAddress($user_order['email']);     // SEND EMAIL TO USER
    $mail->addAddress(EMAIL);
    $mail->AddCC("brina@orderapp.com");
    //SEND  CLIENT EMAIL COPY TO ADMIN
    $mail->AddBCC("oded@orderapp.com");
    //Address to which recipient will reply
    $mail->addReplyTo("orders@orderapp.com", "Reply");


    //Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = 'הזמנה חדשה '." ".$user_order['restaurantTitleHe']." ".'עסק';
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

function email_order_summary_hebrew_admin($user_order,$orderId,$todayDate)
{

    $mailbody  = '<html><head><meta charset="UTF-8"></head>';
    $mailbody  .= '<body style="padding: 0; margin: 0" >';
    $mailbody  .= '<div style="max-width: 600px; margin: 0 auto; border: 1px solid #D3D3D3; border-radius: 5px; overflow: hidden ">';
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
        $mailbody.=(($t['price'] * $t['qty'])).'ש"ח';
        $mailbody.='</span> &nbsp; <span dir="rtl">ש"ח</span>';
        $mailbody.=$t['price'].' x '.$t['qty'].'</td>';
        $mailbody.='<td style="text-align: right;" >'. $t['name_he'] .'</td>';
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

    //TODAY REMAINING BALANCE SECTION
    $mailbody .= '<tr style="font-size: 18px;  font-weight: bold" >';
    $mailbody .= '<td style="white-space: nowrap"> <span style="color: #FF864C;" > '.$user_order['discount'].' ש"ח  '.'</span></td>';
    $mailbody .= '<td style="padding: 5px 0; text-align: right;" >יתרת היום* </td>';
    $mailbody .= '</tr>';

    $mailbody .= '</table>';

    $mailbody .= '<table style="float: right;color:black; padding:10px 30px; width: 100%; position: relative; left: calc(100% - 270px)" cellspacing="5px">';
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

    //COMPANY INFO
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';
    $mailbody .= '<td style="text-align: right; white-space: nowrap"> '.$user_order['company_name'].' </td>';
    $mailbody .= '<td style="padding: 10px 0"><img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_company.png"></td>';
    $mailbody .= '</tr>';
    //COMPANY INFO ENDS



    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';

    //COMPANY ADDRESS
    $mailbody .= '<td style="text-align: right; white-space: nowrap" dir="rtl"> לספק ב : '.$user_order['delivery_address'].'</td>';


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
    $mail->From = "orders@orderapp.com";
    $mail->FromName = "OrderApp";


    //To address and name
    $mail->addAddress(EMAIL);                    //SEND ADMIN EMAIL


    //Address to which recipient will reply
    $mail->addReplyTo("orders@orderapp.com", "Reply");


    //Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = 'עסק'." ".$user_order['restaurantTitleHe']." הזמנה חדשה # "."  ".$orderId;
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




function email_for_mark2($user_order,$orderId,$todayDate)
{

    $mailbody = '';

    // USER NAME

    $mailbody .= 'Name :'. $user_order['name'];
    $mailbody .= '\n';

    // USER EMAIL

    $mailbody .= 'Email :'. $user_order['email'];
    $mailbody .= '\n';

    // USER CONTACT

    $mailbody .= 'Contact :'. $user_order['contact'];
    $mailbody .= '\n';

    // COMPANY NAME

    $mailbody .= ' Company Name' . $user_order['company_name'];
    $mailbody .= '\n';


    // RESTAURANT NAME
    $mailbody .= 'Restaurant Name :'. $user_order['restaurantTitle'];
    $mailbody .= '\n';


    //  PAYMENT METHOD CASH OR CREDIT CARD

    $mailbody .= 'Payment Method : '.$user_order['Cash_Card'];
    $mailbody .= '\n';




    $mailbody .= 'Delivery at Company Address : '. $user_order['deliveryAddress'];
    $mailbody .= '\n';



    if($user_order['isCoupon']) {

        $mailbody .= '\n';
        $mailbody .= 'coupon code : ' . $user_order['couponCode'];
        $mailbody .= '\n';


        if ($user_order['isFixAmountCoupon'] == 'true') {


            $mailbody .= 'Discount : ' . $user_order['discount_coupon'] . ' NIS';

        }
        else {

            $mailbody .= 'Discount : ' . $user_order['discount_coupon'] . ' %';

        }

        $mailbody .= '\n';
    }



    foreach($user_order['cartData'] as $t) {


        if($t['specialRequest'] != "") {


            if ($t['detail'] != '') {

                $mailbody .= 'Special Request : '.$t['specialRequest'];

            }
            else {

                $mailbody .= 'Special Request : '.$t['specialRequest'];
            }

            $mailbody .= '\n';
        }

    }


    $mailbody .= 'Special Request : '.$user_order['specialRequest'];
    $mailbody .= '\n';


    $mailbody .= 'Sub Total : '.$user_order['totalWithoutDiscount'];
    $mailbody .= '\n';


    $mailbody .= 'Total : '.$user_order['total'];
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
    $mail->From = "orders@orderapp.com";
    $mail->FromName = "OrderApp";


    //To address and name
    $mail->addAddress(EMAIL);                    //SEND ADMIN EMAIL


    //Address to which recipient will reply
    $mail->addReplyTo("orders@orderapp.com", "Reply");


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



// ADMIN EMAIL
// EMAIL ORDER SUMMARY HEBREW VERSION FOR ADMIN
function email_for_kitchen($user_order,$orderId,$todayDate)
{
    $mailbody = '';

    // USER NAME
    $mailbody .=  $user_order['user']['name'].' : שֵׁם';
    $mailbody .= '<br>';
    $mailbody .= '<br>';


    $mailbody .=  $user_order['company']['company_name'].' : שם החברה';
    $mailbody .= '<br>';
    $mailbody .= '<br>';


    $mailbody .= ' :  הזמנה';

    foreach($user_order['rests_orders'][0]['foodCartData']  as $t)
    {

        $mailbody .= '<br>';
        $mailbody .= '<br>';


        $mailbody.= $t['name_he'];

        $mailbody .= '<br>';
        $mailbody .= '<br>';

        $mailbody .=  $t['detail_he'];

        $mailbody .= '<br>';
        $mailbody .= '<br>';

        if($t['specialRequest'] != "") {


            if ($t['detail_he'] != '')
            {

                $mailbody .= $t['specialRequest'].' : בקשה מיוחדת';

            }
            else {

                $mailbody .= $t['specialRequest'].' : בקשה מיוחדת';
            }


        }

        $mailbody .= '<br>';
        $mailbody .= '<br>';
        $mailbody .= '<br>';
        $mailbody .= '<br>';

    }


    $mailbody .= $user_order['actual_total'].' : סך כל החשבון ללא דיסקונט';
    $mailbody .= '<br>';
    $mailbody .= '<br>';


    $mailbody .= $user_order['total'].' : סה"כ';
    $mailbody .= '<br>';
    $mailbody .= '<br>';



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
    $mail->From = "orders@orderapp.com";
    $mail->FromName = "OrderApp";


    //To address and name
    $mail->addAddress(EMAIL);                    //SEND ADMIN EMAIL


    //Address to which recipient will reply
    $mail->addReplyTo("orders@orderapp.com", "Reply");


    //Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = " הזמנה חדשה ".substr($user_order['user']['contact'], -4) . " #" . $user_order['rests_orders'][0]['selectedRestaurant']['name_he'];
    $mail->Body = $mailbody;
    $mail->AltBody = "OrderApp";

    if (!$mail->send()) {

        echo "Mailer Error: " . $mail->ErrorInfo;

    }
    else {

        echo "Message has been sent successfully";

    }

}