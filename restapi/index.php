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


            $user['user_id']                =   $userDB['id'];
            $user['name']                   =   $userDB['name'];
            $user['email']                  =   $userDB['smooch_id'];
            $user['contact']                =   $userDB['contact'];
            $company['company_id']          =   $company_id;
            $company['company_name']        =   $companyDB['name'];
            $company['company_address']     =   $companyDB['delivery_address'];
            $company['company_discount']    =   $companyDB['discount'];
            $company['discount_type']       =   $companyDB['discount_type'];


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
                "city_en"              => $city["name_en"],               // CITY NAME EN
                "city_he"              => $city["name_he"],               // CITY NAME HE
                "name_en"              => $result["name_en"],           // RESTAURANT NAME
                "name_he"              => $result["name_he"],           // RESTAURANT NAME
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
        $db_restaurant_tags['count'] = 0;


        $db_restaurant_kashrut = DB::query("select * from kashrut");
        $db_restaurant_kashrut['count'] = 0;

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