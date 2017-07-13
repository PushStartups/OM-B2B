<?php

require      'vendor/autoload.php';
require      'PHPMailer/PHPMailerAutoload.php';
require_once 'inc/initDb.php';


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Mailgun\Mailgun;
use Voucherify\VoucherifyClient;
use Voucherify\VoucherBuilder;
use Voucherify\CustomerBuilder;
use Voucherify\ClientException;



DB::query("set names utf8");


// EMAIL SERVERS FOR EACH EMAIL ADDRESS

// DEV SERVER
if($_SERVER['HTTP_HOST'] == "devb2b.orderapp.com")
{

    define("EMAIL","mark@pushstartups.com");
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



// SLIM INITIALIZATION
$app = new \Slim\App();



//  USER LOGIN FOR B2B
$app->post('/b2b_user_login', function ($request, $response, $args)
{
    try{

        $user_name = $request->getParam('user_name');
        $password  = $request->getParam('password');
        $language  = $request->getParam('lang');

        $obj = '';

        DB::useDB('orderapp_b2b');

        $user = DB::queryFirstRow("select * from b2b_users where user_name = '$user_name' and password = '$password'");


        if (DB::count() > 0)
        {

            $company_id         = $user['company_id'];
            $company_name_db    = DB::queryFirstRow("select * from company where id = $company_id");
            $company_name       = $company_name_db['name'];


            $obj['company_name']    =   $company_name;
            $obj['name']            =   $user['name'];
            $obj['email']           =   $user['smooch_id'];
            $obj['contact']         =   $user['contact'];
            $obj['company_id']      =   $user['company_id'];
            $obj['user_id']         =   $user['id'];
            $obj['company_address'] =   $company_name_db['delivery_address'];
            $obj['error']           =   false;

        }
        else
        {

            $obj['error'] = true;
            $obj['msg'] = '';

            if($language == 'EN')
            {

                $obj['msg']   = "username or password incorrect!";

            }
            else{


                $obj['msg']   = "שם משתמש או סיסמא לא נכונים!";

            }

        }


        // RESPONSE RETURN TO REST API CALL
        $response = $response->withStatus(202);
        $response = $response->withJson(json_encode($obj));
        return $response;

    }

    catch(MeekroDBException $e) {

        $response =  $response->withStatus(500);
        $response =  $response->withHeader('Content-Type', 'text/html');
        $response =  $response->write( $e->getMessage());
        return $response;
    }

});


//  GET IF VOTING ENABLE OR NOT FROM REST LIST PAGE
$app->post('/get_rest_voting', function ($request, $response, $args)
{
    try{

        $company_id = $request->getParam('company_id');

        DB::useDB('orderapp_b2b');

        $result = DB::queryFirstRow("select * from company where id = '$company_id'");

        $obj = '';

        // VOTING IS DISABLE
        if($result['voting'] == 0)
        {
            $obj['voting'] = 0;
        }
        // VOTING ENABLE CHECK VOTING START AND END TIME
        else{


            $obj['voting'] = 1;
            $obj['current_voting_status'] = false;

            // RETRIEVING RESTAURANT VOTE TIMINGS

            DB::useDB('orderapp_b2b');

            $votingTimings = DB::query("select * from vote_timings where company_id = '" . $company_id . "'");

            // CURRENT TIME OF ISRAEL
            date_default_timezone_set("Asia/Jerusalem");
            $currentTime = date("H:i:s");
            $dayOfWeek = date('l');
            $currentTime = DateTime::createFromFormat('H:i:s', $currentTime);

            $nextVotingTime = null;
            $minVotingTime  = null;

            foreach ($votingTimings as $singleTime) {

                $openingTime = DateTime::createFromFormat('H:i:s', $singleTime['voting_start']);
                $closingTime = DateTime::createFromFormat('H:i:s', $singleTime['voting_end']);

                // GET NEXT AVAILABLE VOTE TIME SO REDIRECT USER TO VOTING FROM ORDERING

                if( $openingTime > $currentTime) {

                    if ($nextVotingTime == null) {

                        $nextVotingTime = $singleTime;

                    }
                    else {

                        $openingTimeNextVoting = DateTime::createFromFormat('H:i:s', $nextVotingTime['voting_start']);

                        if ($openingTimeNextVoting > $openingTime) {

                            $nextVotingTime = $singleTime;
                        }

                    }
                }
                else{

                    if ($minVotingTime == null) {

                        $minVotingTime = $singleTime;

                    }
                    else {

                        $openingTimeMinVoting = DateTime::createFromFormat('H:i:s', $minVotingTime['voting_start']);

                        if ($openingTimeMinVoting > $openingTime) {

                            $minVotingTime = $singleTime;
                        }

                    }

                }
                // IF VOTING TIME IS NOW SIMPLY REDIRECT

                if ($currentTime >= $openingTime && $currentTime <= $closingTime) {

                    $obj['current_voting_status'] = true;
                    $obj['voting_slot_id'] = $singleTime['id'];
                    break;
                }

            }

            // IF NEXT VOTING NULL MEANS TODAY VOTING COMPLETE
            // NEXT DAY START VOTING TIME


            if($nextVotingTime != null)
            {

                $openingTimeNextVoting = DateTime::createFromFormat('H:i:s', $nextVotingTime['voting_start']);

                $since_start = $currentTime->diff($openingTimeNextVoting);

                $milisec = 0;

                $milisec  = $milisec + ($since_start->days * 24 * 60);
                $milisec  = $milisec + ($since_start->h * 60 * 60);
                $milisec  = $milisec + ($since_start->i * 60);
                $milisec  = $milisec + ($since_start->s);


                $milisec = ($milisec  * 1000);


                $obj['next_voting_start_time'] =   $nextVotingTime['voting_start'];
                $obj['next_voting_end_time']   =   $nextVotingTime['voting_end'];
                $obj['next_voting_milisec']    =   $milisec;
            }
            else{


                $openingTimeNextVoting = DateTime::createFromFormat('H:i:s', $minVotingTime['voting_start']);

                $openingTimeNextVoting->add(new DateInterval('P1D'));

                $since_start = $currentTime->diff($openingTimeNextVoting);

                $milisec = 0;

                $milisec  = $milisec + ($since_start->days * 24 * 60);
                $milisec  = $milisec + ($since_start->h * 60 * 60);
                $milisec  = $milisec + ($since_start->i * 60);
                $milisec  = $milisec + ($since_start->s);


                $milisec = ($milisec  * 1000);

                $obj['next_voting_start_time'] = $minVotingTime['voting_start'];
                $obj['next_voting_end_time'] =   $minVotingTime['voting_end'];
                $obj['next_voting_milisec']    =   $milisec;
            }

        }

        // RESPONSE RETURN TO REST API CALL
        $response = $response->withStatus(202);
        $response = $response->withJson(json_encode($obj));
        return $response;

    }
    catch(MeekroDBException $e) {

        $response =  $response->withStatus(500);
        $response =  $response->withHeader('Content-Type', 'text/html');
        $response =  $response->write( $e->getMessage());
        return $response;
    }

});


// VOTING PAGE START VOTING
// START VOTING
$app->post('/start_voting', function ($request, $response, $args)
{
    try{

        $company_id  = $request->getParam('company_id');
        $user_id     = $request->getParam('user_id');

        DB::useDB('orderapp_b2b');

        $result = DB::queryFirstRow("select * from company where id = '$company_id'");

        $obj = '';

        // VOTING IS DISABLE
        if($result['voting'] == 0)
        {
            $obj['voting'] = 0;
        }
        // VOTING ENABLE CHECK VOTING START AND END TIME
        else{


            $obj['voting'] = 1;
            $obj['current_voting_status'] = false;

            // RETRIEVING RESTAURANT VOTE TIMINGS

            $votingTimings = DB::query("select * from vote_timings where company_id = '" . $company_id . "'");

            // CURRENT TIME OF ISRAEL
            date_default_timezone_set("Asia/Jerusalem");
            $currentTime = date("H:i:s");
            $dayOfWeek = date('l');
            $currentTime = DateTime::createFromFormat('H:i:s', $currentTime);


            foreach ($votingTimings as $singleTime) {

                $openingTime = DateTime::createFromFormat('H:i:s', $singleTime['voting_start']);
                $closingTime = DateTime::createFromFormat('H:i:s', $singleTime['voting_end']);

                // IF VOTING TIME IS NOW SIMPLY REDIRECT

                if ($currentTime >= $openingTime && $currentTime <= $closingTime) {


                    $obj['current_voting_status'] = true;
                    $obj['voting_slot_id'] = $singleTime['id'];
                    $obj['voting_start_time'] =  $singleTime['voting_start'];
                    $obj['voting_end_time'] =  $singleTime['voting_end'];
                    break;

                }

            }


            // IF VOTING GOING ON CHECK COMPANY CURRENT VOTING ID IF NOT MATCH REMOVE OLD AND START NEW VOTING
            if($obj['current_voting_status'])
            {

                // IF LAST VOTING ID NOT MATCH WITH CURRENT VOTING ID

                date_default_timezone_set("Asia/Jerusalem");

                $currentDate = date("Y-m-d");

                $currentDateObj = DateTime::createFromFormat('Y-m-d', $currentDate);
                $lastVotingDate =  DateTime::createFromFormat('Y-m-d', $result['last_voting_date']);


                if(($result['last_voting_id'] != $obj['voting_slot_id']) || ($currentDateObj != $lastVotingDate ))
                {

                    // REMOVE AND UPDATE OLD ENTRIES

                    DB::query("UPDATE company SET last_voting_id = '".$obj['voting_slot_id']."' , last_voting_date = '$currentDate' WHERE id = '$company_id'");


                    DB::query("delete from user_votes where vote_timing_id = '".$result['last_voting_id']."'");


                    DB::query("delete from company_voting where company_id = '$company_id'");


                    // ADD NEW ENTRIES FOR ALL COMPANY RESTAURANTS
                    $restCompanies = DB::query("select * from company_rest where company_id = '$company_id'");


                    foreach ($restCompanies as $resCompany)
                    {
                        DB::insert('company_voting', array(

                            'company_id'        =>   $company_id,
                            'restaurant_id'     =>   $resCompany['rest_id'],
                            'vote_count'        =>   0,
                            'vote_timing_id'    =>   $obj['voting_slot_id'],
                        ));

                    }

                }

                // GET USER VOTE
                $userVote = DB::queryFirstRow("select * from user_votes where user_id = '$user_id'");

                // USER VOTE EXIST

                if(DB::count() != 0) {

                    if ($userVote['vote_timing_id'] == $obj['voting_slot_id']) {

                        $obj['rest_id_vote_for'] = $userVote['restaurant_id'];
                    }
                    else{

                        // DELETE IF EXIST WITH OLD VOTING ID
                        DB::query("delete from user_votes  where user_id = '$user_id'");
                        $obj['rest_id_vote_for'] = '';
                    }
                }
                else{

                    $obj['rest_id_vote_for'] = '';
                }

                // VOTING END TIME HOURS LEFT TO CLOSE COMPUTATION

                $votingCloseTime = DateTime::createFromFormat('H:i:s', $obj['voting_end_time']);

                $since_end = $currentTime->diff($votingCloseTime);

                $obj['hr_left_voting_end']      =   $since_end->h;
                $obj['mint_left_voting_end']    =   $since_end->i;
                $obj['sec_left_voting_end']     =   $since_end->s;


                $companyRestVotes = DB::query("select * from company_voting where company_id = '$company_id'");

                $obj['comp_rest_vote_detail'] = $companyRestVotes;

            }
        }

        // RESPONSE RETURN TO REST API CALL
        $response = $response->withStatus(202);
        $response = $response->withJson(json_encode($obj));
        return $response;

    }
    catch(MeekroDBException $e) {

        $response =  $response->withStatus(500);
        $response =  $response->withHeader('Content-Type', 'text/html');
        $response =  $response->write( $e->getMessage());
        return $response;
    }

});


//  CAST USER VOTE
$app->post('/cast_user_vote', function ($request, $response, $args)
{
    $companyRestVotes  = '';

    try{

        DB::useDB('orderapp_b2b');

        $rest_id_for_vote  = $request->getParam('rest_id_for_vote');
        $user_id           = $request->getParam('user_id');
        $voting_slot_id    = $request->getParam('voting_id');
        $company_id        = $request->getParam('company_id');

        // GET USER VOTE
        $userVote = DB::queryFirstRow("select * from user_votes where user_id = '$user_id'");

        // USER VOTE EXIST

        if(DB::count() != 0) {

            // DELETE IF EXIST WITH OLD VOTING ID
            DB::query("delete from user_votes  where user_id = '$user_id'");

            DB::insert('user_votes', array(

                'user_id' => $user_id,
                'restaurant_id' => $rest_id_for_vote,
                'vote_timing_id' => $voting_slot_id
            ));


            // DECREASE ONE FROM OLD VOTE COUNT

            DB::query("UPDATE company_voting SET vote_count = vote_count - 1 WHERE restaurant_id = '".$userVote['restaurant_id']."' AND vote_count <> 0");

        }
        else{


            DB::insert('user_votes', array(

                'user_id'           =>   $user_id,
                'restaurant_id'     =>   $rest_id_for_vote,
                'vote_timing_id'    =>   $voting_slot_id
            ));



        }


        // INCREASE VOTE COUNT

        DB::query("UPDATE company_voting SET vote_count = vote_count + 1 WHERE restaurant_id = '$rest_id_for_vote'");

        $companyRestVotes = DB::query("select * from company_voting where company_id = '$company_id'");

    }
    catch(MeekroDBException $e) {

        $response =  $response->withStatus(500);
        $response =  $response->withHeader('Content-Type', 'text/html');
        $response =  $response->write( $e->getMessage());
        return $response;
    }


    // RESPONSE RETURN TO REST API CALL
    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($companyRestVotes));
    return $response;

});



//  VOTE UPDATES
$app->post('/get_vote_updates', function ($request, $response, $args)
{

    DB::useDB('orderapp_b2b');

    $company_id   = $request->getParam('company_id');
    $voting_slot_id  = $request->getParam('voting_id');

    $companyRestVotes = '';

    $rests = DB::query("select * from company_rest where company_id ='$company_id'");

    foreach ($rests as $rest)
    {
        $comp_vote = DB::queryFirstRow("select * from company_voting where restaurant_id = '".$rest['rest_id']."' and company_id = '$company_id' and vote_timing_id = '$voting_slot_id'");

        if ( DB::count() == 0 )
        {

            DB::query("insert into company_voting (company_id, restaurant_id, vote_count, vote_timing_id) values ('$company_id', '".$rest['rest_id']."', '0', '$voting_slot_id') ");

        }
    }

    try{

        DB::useDB('orderapp_b2b');

        $companyRestVotes = DB::query("select * from company_voting where company_id = '$company_id'");
    }
    catch(MeekroDBException $e) {

        $response =  $response->withStatus(500);
        $response =  $response->withHeader('Content-Type', 'text/html');
        $response =  $response->write( $e->getMessage());
        return $response;
    }


    // RESPONSE RETURN TO REST API CALL
    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($companyRestVotes));
    return $response;

});



//  CHECK HIDE PAYMENT OPTION FOR COMPANY
$app->post('/is_hide_payment', function ($request, $response, $args)
{

    DB::useDB('orderapp_b2b');

    $company_id = $request->getParam('company_id');
    $companyDetail = DB::queryFirstRow("select * from company where id = '$company_id'");

    // RESPONSE RETURN TO REST API CALL
    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($companyDetail['hide_payment']));
    return $response;


});

$app->post('/in_time_discount', function ($request, $response, $args)
{
    DB::useDB('orderapp_b2b');

    $company_id = $request->getParam('company_id');
    $restaurant_id = $request->getParam('rest_id');

    $discountDetail = DB::queryFirstRow("select * from b2b_rest_discounts where company_id = '$company_id' AND rest_id = '$restaurant_id'");

    if(DB::count() == 0)
    {
        // RESPONSE RETURN TO REST API CALL
        $response = $response->withStatus(202);
        $response = $response->withJson(json_encode(0));
        return $response;

    }

    // RESPONSE RETURN TO REST API CALL
    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($discountDetail['in_time_discount']));
    return $response;


});


//  WEB HOOK GET ALL RESTAURANT AGAINST USER COMPNAY
$app->post('/get_all_restaurants', function ($request, $response, $args)
{
    try {

        DB::useDB('orderapp_b2b');

        $company_id = $request->getParam('company_id');
        $voting = $request->getParam('voting');
        $winnerIds = null;

        $companyDetail = DB::queryFirstRow("select * from company where id = '$company_id'");

        // VOTING IS TRUE FIND LAST WINNER
        if($voting && $companyDetail['last_voting_id'] != -1)
        {
            $winnerIds = findWinnerRestId($companyDetail['last_voting_id'],$companyDetail['winner_limit']);

        }



        $dis = $companyDetail['discount'];


        // SELECT ALL RESTAURANTS HAVE COMPANY ID THIS

        if($winnerIds != null)
        {
            $rest_ids = DB::query("select rest_id from company_rest where company_id = '$company_id' AND rest_id in ('" . implode("','", $winnerIds) . "')");
        }
        else{

            $rest_ids = DB::query("select rest_id from company_rest where company_id = '$company_id'");
        }




        $restaurants = Array();

        $results = Array();

        $cnt = 0;


        DB::useDB('orderapp_restaurants');

        // GET RESTAURANTS DETAIL ON THE BASIS OF ID FOR THIS COMPANY

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

            // GET TAGS OF RESTAURANT i.e BURGER , PIZZA


            DB::useDB('orderapp_restaurants');

            $tagsIds = DB::query("select tag_id from restaurant_tags where restaurant_id = '" . $result['id'] . "'");

            $tags = Array();
            $count2 = 0;
            $hoursLeftToOpen = null;

            foreach ($tagsIds as $id) {


                DB::useDB('orderapp_restaurants');
                $tag = DB::queryFirstRow("select * from tags where id = '" . $id["tag_id"] . "'");
                $tags[$count2] = $tag;
                $count2++;
            };

            // GET GALLERY OF RESTAURANT


            DB::useDB('orderapp_restaurants');
            $galleryImages = DB::query("select url from restaurant_gallery where restaurant_id = '" . $result['id'] . "'");


            // RETRIEVING RESTAURANT TIMINGS i.e SUNDAY   STAT_TIME : 12:00  END_TIME 21:00;

            DB::useDB('orderapp_b2b');

            $restaurantTimings = DB::query("select * from company_timing where company_id = '" . $company_id . "'");


            // CURRENT TIME OF ISRAEL
            date_default_timezone_set("Asia/Jerusalem");
            $currentTime = date("H:i");
            $tempDate = date("d/m/Y");
            $dayOfWeek = date('l');


            // RESTAURANT AVAILABILITY ACCORDING TO TIME
            $currentStatus = false;

            $today_timings = "";


            foreach ($restaurantTimings as $singleTime) {


                if ($singleTime['week_en'] == $dayOfWeek) {


                    $today_timings = $singleTime['opening_time'] . " - " . $singleTime['closing_time'];
                    $openingTime = DateTime::createFromFormat('H:i', $singleTime['opening_time']);
                    $closingTime = DateTime::createFromFormat('H:i', $singleTime['closing_time']);
                    $currentTimes = DateTime::createFromFormat('H:i', $currentTime);


                    if ($currentTimes >= $openingTime && $currentTimes <= $closingTime) {

                        $currentStatus = true;

                        break;
                    }
                    else {

                        $hoursLeftToOpen = "Open On Sunday";


                    }

                }
            }

            // GET B2B PERCENTAGE DISCOUNT ON THIS ITEM

            $in_time_discount = 0;

            DB::useDB('orderapp_b2b');
            $percentage_discount = DB::queryFirstRow("select * from b2b_rest_discounts where rest_id = '" .  $result['id'] . "' AND company_id = '".$company_id."'");

            if(DB::count() == 0)
            {
                // NO DISCOUNT FOUND
                $percentage_discount = 'empty';

            }
            else{

                $in_time_discount = $percentage_discount['in_time_discount'];
                $percentage_discount = $percentage_discount['discount_percent'];

            }



            // CREATE DEFAULT RESTAURANT OBJECT;
            $restaurant = [

                "discount"             => $dis,                         // DISCOUNT OF COMPANY
                "tags"                 => $tags,                        // RESTAURANT TAGS
                "id"                   => $result["id"],                // RESTAURANT ID
                "name_en"              => $result["name_en"],           // RESTAURANT NAME
                "name_he"              => $result["name_he"],           // RESTAURANT NAME
                "min_amount"           => $companyDetail['min_order'],  // COMPANY MINIMUM AMOUNT
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
                "availability"         => $currentStatus,               // RESTAURANT CURRENT AVAILABILITY
                "today_timings"        => $today_timings,               // TODAY TIMINGS
                "percentage_discount"  => $percentage_discount,         // B2B PERCENTAGE DISCOUNT
                "hours_left_to_open"   => $hoursLeftToOpen,             // HOURS LEFT TO OPEN FROM CURRENT TIME
                "in_time_discount"     => $in_time_discount             // IN TIME DISCOUNT IF DELIVERY IS SET BETWEEN REST TIMINGS
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


function findWinnerRestId($lastVotingId,$limit)
{

    DB::useDB('orderapp_b2b');

    // GET LAST VOTING ID
    $votes = DB::query("select restaurant_id from company_voting where vote_timing_id = '$lastVotingId' order by vote_count DESC limit $limit");


    $winnerVoteObj = [];
    $count = 0;

    foreach ($votes as $vote)
    {

        $winnerVoteObj[$count] = $vote['restaurant_id'];
        $count++;

    }

    return $winnerVoteObj;
}


//  SEND DISCOUNT TO CLIENT
$app->post('/get_discount', function ($request, $response, $args){

    $smooch_id = $request->getParam('user_email');

    $obj = '';

    DB::useDB('orderapp_b2b');

    $user = DB::queryFirstRow("select * from b2b_users where smooch_id = '$smooch_id'");
    $company_id = $user['company_id'];


    if (DB::count() > 0) {

        // CURRENT TIME OF ISRAEL
        date_default_timezone_set("Asia/Jerusalem");

        $today = date("Y-m-d");

        $company = DB::queryFirstRow("select * from company where id = '$company_id'");

        // IF DISCOUNT TYPE IS DAILY

        if($company['discount_type'] == "daily") {

            if ($today > $user['date']) {

                $discount = $company['discount'];
                DB::query("UPDATE b2b_users SET date = '$today', discount = '$discount'  WHERE smooch_id = '$smooch_id'");


            }
            else {

                $discount = $user['discount'];

            }

        }

        // IF DISCOUNT TYPE IS MONTHLY

        else{

            $monthUser = date('m', strtotime($user['date']));
            $todayMonth =  date("m");;

            if($todayMonth > $monthUser)
            {
                $discount = $company['discount'];
                DB::query("UPDATE b2b_users SET date = '$today', discount = '$discount'  WHERE smooch_id = '$smooch_id'");
            }
            else{

                $discount = $user['discount'];
            }

        }


        $obj['discount']            = $discount;
        $obj['company_discount']    = $company['discount'];
        $obj['discount_type']       = $company['discount_type'];
        $obj['error']               = false;
    }
    else
    {
        $obj['msg']         = 'contact your company';
        $obj['error']       = true;
    }



    // RESPONSE RETURN TO REST API CALL
    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($obj));
    return $response;

});




// temporary api
$app->post('/update_date', function ($request, $response, $args){

    $date = $request->getParam('date');
    $user_name = $request->getParam('user_name');
    $discount = $request->getParam('discount');


    DB::query("UPDATE b2b_users SET date = '$date', discount = '$discount'  WHERE user_name = '$user_name'");


});



$app->post('/get_delivery_timings', function ($request, $response, $args){

    $company_id = $request->getParam('company_id');

    DB::useDB('orderapp_b2b');

    $timings = DB::query("select delivery_timing from delivery_timings WHERE company_id = $company_id");

    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($timings));
    return $response;


});





function traccer($order_id,$name,$phone,$start_address,$delivery_address,$startLat,$startLng,$endLat,$endLng)
{
    $service_url = "http://35.156.74.68:8082/api/objectives";
    $curl = curl_init($service_url);
    $curl_post_data = array(

        "name"           => $name,
        "phone"          => $phone,
        "startLatitude"  => $startLat,
        "startLongitude" => $startLng,
        "endLatitude"    => $endLat,
        "endLongitude"   => $endLng,
        "deviceId"       => 100,
        "status"         => "incomplete",
        "startAddress"   => $start_address,
        "endAddress"     => $delivery_address,
        "orderId"        => $order_id,
        "geocode"        => "no",
        "timeCreate"     => null

    );


    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_USERPWD,  "admin:admin");
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($curl_post_data));
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Authorization: Basic YWRtaW46YWRtaW4=',
        'Content-Type: application/json'
    ));


    $curl_response = curl_exec($curl);
    curl_close($curl);


    return $curl_response;

}





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


            DB::useDB('orderapp_b2b');


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



// GET ALL CARDS ASSOCIATED WITH USER
$app->post('/get_all_cards_info', function ($request, $response, $args){


    $user_email = $request->getParam('user_email');

    DB::useDB('orderapp_b2b');


    //CHECK IF USER ALREADY EXIST, IF NO CREATE USER
    $getUser = DB::queryFirstRow("select id,smooch_id from b2b_users where smooch_id = '" . $user_email . "'");


    $cards = DB::query("select id,card_mask from user_credit_cards WHERE user_id = '".$getUser['id']."'");


    if(DB::count() == 0) {

        $response = $response->withStatus(202);
        $response = $response->withJson(json_encode('null'));
        return $response;

    }

    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($cards));
    return $response;



});





//  SEND EMAIL TO USER IF FORGET PASSWORD
$app->post('/forgot_email', function ($request, $response, $args){

    DB::useDB('orderapp_b2b');

    $msg = '';
    $user_email = $request->getParam('email');
    $userLoginInfo = DB::queryFirstRow("select * from b2b_users WHERE smooch_id = %s",$user_email);

    if(DB::count() > 0)
    {
        $username = $userLoginInfo['user_name'];
        $password = $userLoginInfo['password'];

        $is_error = mailForgotPassword($password, $username, $user_email);

        ob_end_clean();

        if($is_error)
        {
            $msg['message'] = 'Email could not send';
            $msg['error'] = 'true';
        }
        else
        {
            $msg['message'] = 'Please check your email';
            $msg['error'] = 'false';
        }

    }
    else
    {
        $msg['message'] = 'Your email does not exist contact your company';
        $msg['error'] = 'true';
    }

    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($msg));
    return $response;


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

        DB::useDB('orderapp_b2b');

        $percentage_discount = DB::queryFirstRow("select * from b2b_rest_discounts where rest_id = '" . $id . "' AND company_id = '".$company_id."'");

        if(DB::count() == 0)
        {
            // NO DISCOUNT FOUND
            $percentage_discount = 'empty';

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

//  STORE USER ORDER IN DATABASE
$app->post('/b2b_add_order', function ($request, $response, $args) {


    DB::useDB('orderapp_b2b');

    // GET ORDER RESPONSE FROM USER (CLIENT SIDE)
    $user_order = $request->getParam('b2b_user_order');


    $user_id = null;
    $smooch_id = $user_order['email'];
    $todayDate = Date("d/m/Y");
    $today = date("Y-m-d");


    //CHECK IF USER ALREADY EXIST, IF NO CREATE USER
    $getUser = DB::queryFirstRow("select id,smooch_id from b2b_users where smooch_id = '" . $user_order['email'] . "'");


    $discount = $user_order['discount'];

    $user_id  = $getUser['id'];


    // CREATE A NEW ORDER AGAINST USER
    DB::insert('b2b_orders', array(

        'user_id'         => $getUser['id'],
        'company_id'      => $user_order['company_id'],
        'total'           => $user_order['total'],
        'actual_total'    => $user_order['actualTotal'],
        'discount'        => $user_order['discount'],
        'transaction_id'  => $user_order['trans_id'],
        "date"            => DB::sqleval("NOW()")
    ));

    $orderId = DB::insertId();


    //GET COMPANY NAME
    $getCompanyName = DB::queryFirstRow("select * from company where id = '" . $user_order['company_id'] . "'");
    $user_order['company_name'] = $getCompanyName['name'];
    $user_order['delivery_address'] = $getCompanyName['delivery_address'];
    $user_order['discount_type'] = $getCompanyName['discount_type'];


    // ORDER TRACCER

    try {


        traccer($orderId, $user_order['name'], $user_order['contact'], $user_order['restaurantAddress'], $user_order['deliveryAddress'], $user_order['rest_lat'], $user_order['rest_lng'], $getCompanyName['lat'], $getCompanyName['lng']);


    }
    catch (Exception $exception)
    {


    }


    // LAST INSERTED ORDER ID

    foreach ($user_order['cartData'] as $orders) {
        try{
            // ADD ORDER DETAIL AGAINST USER
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


    // SEND EMAIL TO KITCHEN

    email_for_kitchen($user_order, $orderId, $todayDate);

    ob_end_clean();


    email_for_mark2($user_order, $orderId, $todayDate);

    ob_end_clean();

    // SEND ADMIN COPY EMAIL ORDER SUMMARY

    email_order_summary_hebrew_admin($user_order, $orderId, $todayDate);

    ob_end_clean();


    // CLIENT EMAIL
    // EMAIL ORDER SUMMARY
    //
    if ($user_order['language'] == 'en') {

        email_order_summary_english($user_order, $orderId, $todayDate);
    }
    else
    {

        email_order_summary_hebrew($user_order, $orderId, $todayDate);
    }


    ob_end_clean();


    $delivery_time  = date('H:i:s');

    $delivery_time = strtotime($delivery_time) + 60*60;

    $delivery_time = date('H:i:s',$delivery_time);


    createBringgTask($user_order, $todayDate, $delivery_time) ;


    ob_end_clean();


    DB::useDB('orderapp_b2b');


    DB::query("UPDATE b2b_users SET date = '$today', discount = '$discount'  WHERE smooch_id = '$smooch_id'");
    //DB::query("UPDATE b2b_users SET discount = '$discount'  WHERE id = '$user_id'");


    // RESPONSE RETURN TO REST API CALL
    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode('success'));
    return $response;
});



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

        DB::useDB('orderapp_b2b');

        //CHECK IF USER ALREADY EXIST, IF NO CREATE USER
        $getUser = DB::queryFirstRow("select * from b2b_users where smooch_id = '$email'");

        if (DB::count() == 0) {

            // USER NOT EXIST IN DATABASE, SO CREATE USER IN DATABASE
            DB::insert('users', array(
                'smooch_id' => $email,
                'email' => $email
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

    DB::useDB('orderapp_user');

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


            DB::useDB('orderapp_b2b');

            DB::query("update b2b_users set voucherify_id = '".$vResult->id."' where id = '$user_id'");


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

$app->post('/validate_delivery_timing', function ($request, $response, $args)
{
    $delivery_timing        =  $request->getParam('delivery_timing');
    $delivery_timing        = str_replace('"',"", $delivery_timing);
    $delivery_timing        = str_replace(" ","", $delivery_timing);


    $company_timing         =  $request->getParam('company_timing');
    $company_timing         = str_replace('"',"", $company_timing);
    $company_timing         = str_replace(" ","", $company_timing);


    $delivery_time          =  explode("-" , $delivery_timing);
    $company_time           =  explode("-" , $company_timing);

    $delivery_start_time    = $delivery_time[0];
    $phpdate_start = strtotime($delivery_start_time);
    $delivery_start_time = date('H:i', $phpdate_start);


    $delivery_end_time    = $delivery_time[1];
    $phpdate_start = strtotime($delivery_end_time);
    $delivery_end_time = date('H:i', $phpdate_start);


    $company_start_time    = $company_time[0];
    $phpdate_start         = strtotime( $company_start_time);
    $company_start_time    = date('H:i', $phpdate_start);

    $company_end_time    = $company_time[1];
    $phpdate_end         = strtotime( $company_end_time);
    $company_end_time    = date('H:i', $phpdate_end);

    if($company_start_time  <  $delivery_start_time)
    {
        if($company_end_time > $delivery_end_time)
        {
            $response = $response->withStatus(202);
            $response = $response->withJson(json_encode("true"));
            return $response;
        }
        else{
            $response = $response->withStatus(202);
            $response = $response->withJson(json_encode("false"));
            return $response;
        }

    }
    else{
        $response = $response->withStatus(202);
        $response = $response->withJson(json_encode("false"));
        return $response;
    }






});




//  MAIL GUN API EMAIL VALIDATOR
$app->post('/validate_email', function ($request, $response, $args) {

    $email  = $request->getParam('email');

//    # Instantiate the client.

    $mgClient = new Mailgun('pubkey-bdbdde601ba26a9d5d1adb7f003284a9');

    $validateAddress = $email;
//
//    # Issue the call to the client.
    $result = $mgClient->get("address/validate", array('address' => $validateAddress));
//
//    # is_valid is 0 or 1
    $isValid = $result->http_response_body->is_valid;

    // RESPONSE RETURN TO REST API CALL
    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($isValid));
    return $response;

});




//  RETURN PAYMENT URL OF GUARD API AGAINST PAYMENT OF USER ORDER
$app->post('/stripe_payment_request', function ($request, $response, $args) {

    try {

        $email          = $request->getParam('email');
        $amount         = $request->getParam('amount');
        $user_order     = $request->getParam('b2b_user_order');
        $creditCardNo   = $request->getParam('cc_no');
        $expDate        = $request->getParam('exp_date');
        $cvv            = $request->getParam('cvv');

        $user_id        = 0;
        $result         = "";

        DB::useDB('orderapp_b2b');

        $getUser = DB::queryFirstRow("select id,smooch_id from b2b_users where smooch_id = '$email'");

        if (DB::count() == 0) {

            // USER NOT EXIST IN DATABASE, SO CREATE USER IN DATABASE
            DB::insert('b2b_users', array(
                'smooch_id' => $email
            ));
            $user_id = DB::insertId();
        } else {

            // IF USER ALREADY EXIST IN DATABASE
            $user_id = $getUser['id'];
        }


        if($user_order['selected_card_id'] != -1) {


            $cId = $user_order['selected_card_id'];

            $card = DB::queryFirstRow("select * from user_credit_cards where id = $cId");



            if ($user_order['language'] == 'en') {

                $result = stripePaymentRequest(($amount * 100), $user_id, $user_order, $email, $card['card_id'], $card['expiration'], $card['cvv']);

            } else {

                $result = stripePaymentRequestHE(($amount * 100), $user_id, $user_order, $email, $card['card_id'], $card['expiration'], $card['cvv']);

            }

        }
        else {


            if ($user_order['language'] == 'en') {

                $result = stripePaymentRequest(($amount * 100), $user_id, $user_order, $email, $creditCardNo, $expDate, $cvv);

            } else {

                $result = stripePaymentRequestHE(($amount * 100), $user_id, $user_order, $email, $creditCardNo, $expDate, $cvv);

            }

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




$app->run();




// SUPPORT METHODS
// STRIPE API PAYMENT REQUEST
// AMOUNT DIVIDED BY 100 FROM API

function  stripePaymentRequest($amount, $user_id, $user_order ,$email,$creditCardNo,$expDate,$cvv)
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


                            if($user_order['selected_card_id'] != -1)
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
    $mailbody .= '<p>Visit Website : <a style="color: #3b5998; text-decoration: none;" href="'.B2BLINK.'">'.B2BLINK.'</a></p>';
    $mailbody .= '</td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr>';
    $mailbody .= '<td align="center" style="padding: 100px 0 20px;">';
    $mailbody .= '<table border="0" cellspacing="0" cellpadding="0">';
    $mailbody .= '<tr><td width="37" style="text-align: center; padding: 0 8px;"><a href="https://www.facebook.com/theorderapp/"><img src="https://dev.orderapp.com/admin/img/fb.png" width="37" height="37" alt="Facebook" border="0" /></a></td>';
    $mailbody .= '<td width="37" style="text-align: center; padding: 0 8px;"><a href="https://twitter.com/OrderAppTeam"><img src="https://dev.orderapp.com/admin/img/tw.png" width="37" height="37" alt="Twitter" border="0" /></a></td>';
    $mailbody .= '<td width="37" style="text-align: center; padding: 0 8px;"><a href="https://plus.google.com/106974163137537901922"> <img src="https://dev.orderapp.com/admin/img/gp.png" width="37" height="37" alt="Google Plus" border="0" /></a></td>';
    $mailbody .= '<td width="37" style="text-align: center; padding: 0 8px;"><a href="https://www.instagram.com/theorderapp/"><img src="https://dev.orderapp.com/admin/img/insta.png" width="37" height="37" alt="Instagram" border="0" /></a></td>';
    $mailbody .= '<td width="37" style="text-align: center; padding: 0 8px;"><a href="https://www.pinterest.com/orderapp/"><img src="https://dev.orderapp.com/admin/img/pin.png" width="37" height="37" alt="Pinterest" border="0" /></a></td>';
    $mailbody .= '<td width="37" style="text-align: center; padding: 0 8px;"><a href="https://www.linkedin.com/in/orderapp-team-4a886510b"><img src="https://dev.orderapp.com/admin/img/link.png" width="37" height="37" alt="Linkedin" border="0" /></a></td>';
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
    $mail->From = "orders@orderapp.com";
    $mail->FromName = "OrderApp";


    //To address and name
    $mail->addAddress($email);                    //SEND ADMIN EMAIL


    //Address to which recipient will reply
    $mail->addReplyTo("orders@orderapp.com", "Reply");


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



function  stripePaymentRequestHE($amount, $user_id, $user_order ,$email,$creditCardNo,$expDate,$cvv)
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
								<creditType>RegularCredit</creditType>';

                                if($user_order['selected_card_id'] != -1)
                                {
                                    $poststring .= '<cardId>'.$creditCardNo.'</cardId>';

                                }
                                else{

                                    $poststring .= '<cardNo>'.$creditCardNo.'</cardNo>';
                                }


                             $poststring.='<cvv>'.$cvv.'</cvv>
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


//GENERATE EMAIL
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
    $mailbody .= '<td align="center" valign="top" style="text-align: right; width: 52px;"><img style="display: block;" src="http://devb2b.orderapp.com/restapi/images/delivery-email.png"></td></tr>';
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
    $mailbody .= '<a style="display: block; width: 87px; margin: 0 auto; color: #fff;" href="#"><img style="display: block; margin: 0 auto;" src="http://devb2b.orderapp.com/restapi/images/logo-image.png"></a></td></tr></table>';
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
    $mail->addAddress($user_email);              // SEND EMAIL TO USER

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

// CLIENT EMAILS

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



// ADMIN EMAIL
// EMAIL ORDER SUMMARY HEBREW VERSION FOR ADMIN
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


    $mailbody .= 'Total Bill Without Discount : '.$user_order['totalWithoutDiscount'];
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

    $mailbody .= 'שֵׁם :'. $user_order['name'];
    $mailbody .= '\n';








    foreach($user_order['cartData'] as $t) {


        if($t['specialRequest'] != "") {


            if ($t['detail'] != '') {

                $mailbody .= 'בקשה מיוחדת : '.$t['specialRequest'];

            }
            else {

                $mailbody .= 'בקשה מיוחדת : '.$t['specialRequest'];
            }

            $mailbody .= '\n';
        }
        else{
            $mailbody .=  ': הזמנה'.$t['detail_he']; 
        }

    }


    $mailbody .= 'סך כל החשבון ללא דיסקונט : '.$user_order['totalWithoutDiscount'];
    $mailbody .= '\n';


    $mailbody .= 'סה"כ : '.$user_order['total'];
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
    $mail->Subject = " הזמנה חדשה ".substr($user_order['contact'], -4) . " #" . $user_order['restaurantTitleHe'];
    $mail->Body = $mailbody;
    $mail->AltBody = "OrderApp";

    if (!$mail->send()) {

        echo "Mailer Error: " . $mail->ErrorInfo;

    }
    else {

        echo "Message has been sent successfully";

    }

}




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
          "phone": "' . $user_order['contact'] . '",
          "company": "' . $user_order['company_name'] . '",
          "Remaining Today Balance": "' . $user_order['discount'] . '"
       },
       "way_points":[
          {
             "customer":{
                "name": "' . $user_order['restaurantTitleHe'] . '",
                "phone": "026222862"
             },
             "address":"Yigal Alon Ave 6, Beit Shemesh, Israel",
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

?>

