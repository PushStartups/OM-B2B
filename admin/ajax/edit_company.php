<?php
require_once '../inc/initDb.php';
require_once '../inc/functions.php';
DB::query("set names utf8");




if(($_POST['monday_start_time'] == "Closed") || ($_POST['monday_start_time'] == "closed") || ($_POST['monday_start_time'] == "Close") || ($_POST['monday_start_time'] == "close"))
{
    $_POST['monday_start_time']    = "Closed";
    $_POST['monday_end_time']      = "Closed";

    $_POST['monday_start_time_he'] = "סגור";
    $_POST['monday_end_time_he']   = "סגור";

    $mon_delivery_time = "Closed";
}
else
{
    $_POST['monday_start_time_he'] = $_POST['monday_start_time'];
    $_POST['monday_end_time_he']   = $_POST['monday_end_time'];

    $mon_end_time = $_POST['monday_end_time'];

    $timestampmon = strtotime($mon_end_time) + 60*60;
    $mon_delivery_time = date('H:i', $timestampmon);
}


if(($_POST['tuesday_start_time'] == "Closed") || ($_POST['tuesday_start_time'] == "closed") || ($_POST['tuesday_start_time'] == "Close") || ($_POST['tuesday_start_time'] == "close"))
{
    $_POST['tuesday_start_time']    = "Closed";
    $_POST['tuesday_end_time']      = "Closed";

    $_POST['tuesday_start_time_he'] = "סגור";
    $_POST['tuesday_end_time_he']   = "סגור";

    $tue_delivery_time = "Closed";
}
else
{
    $_POST['tuesday_start_time_he'] = $_POST['tuesday_start_time'];
    $_POST['tuesday_end_time_he']   = $_POST['tuesday_end_time'];

    $tue_end_time = $_POST['tuesday_end_time'];

    $timestamptue = strtotime($tue_end_time) + 60*60;
    $tue_delivery_time = date('H:i', $timestamptue);
}




if(($_POST['wednesday_start_time'] == "Closed") || ($_POST['wednesday_start_time'] == "closed") || ($_POST['wednesday_start_time'] == "Close") || ($_POST['wednesday_start_time'] == "close"))
{
    $_POST['wednesday_start_time']    = "Closed";
    $_POST['wednesday_end_time']      = "Closed";

    $_POST['wednesday_start_time_he'] = "סגור";
    $_POST['wednesday_end_time_he']   = "סגור";

    $wed_delivery_time = "Closed";
}
else
{
    $_POST['wednesday_start_time_he'] = $_POST['wednesday_start_time'];
    $_POST['wednesday_end_time_he']   = $_POST['wednesday_end_time'];

    $wed_end_time = $_POST['wednesday_end_time'];

    $timestampwed = strtotime($wed_end_time) + 60*60;
    $wed_delivery_time = date('H:i', $timestampwed);
}




if(($_POST['thursday_start_time'] == "Closed") || ($_POST['thursday_start_time'] == "closed") || ($_POST['thursday_start_time'] == "Close") || ($_POST['thursday_start_time'] == "close"))
{
    $_POST['thursday_start_time']    = "Closed";
    $_POST['thursday_end_time']      = "Closed";

    $_POST['thursday_start_time_he'] = "סגור";
    $_POST['thursday_end_time_he']   = "סגור";

    $thur_delivery_time = "Closed";
}
else
{
    $_POST['thursday_start_time_he'] = $_POST['thursday_start_time'];
    $_POST['thursday_end_time_he']   = $_POST['thursday_end_time'];

    $thur_end_time = $_POST['thursday_end_time'];

    $timestampthur = strtotime($thur_end_time) + 60*60;
    $thur_delivery_time = date('H:i', $timestampthur);
}




if(($_POST['friday_start_time'] == "Closed") || ($_POST['friday_start_time'] == "closed") || ($_POST['friday_start_time'] == "Close") || ($_POST['friday_start_time'] == "close"))
{
    $_POST['friday_start_time']    = "Closed";
    $_POST['friday_end_time']      = "Closed";

    $_POST['friday_start_time_he'] = "סגור";
    $_POST['friday_end_time_he']   = "סגור";

    $fri_delivery_time = "Closed";
}
else
{
    $_POST['friday_start_time_he'] = $_POST['friday_start_time'];
    $_POST['friday_end_time_he']   = $_POST['friday_end_time'];

    $fri_end_time = $_POST['friday_end_time'];

    $timestampfri = strtotime($fri_end_time) + 60*60;
    $fri_delivery_time = date('H:i', $timestampfri);
}




if(($_POST['saturday_start_time'] == "Closed") || ($_POST['saturday_start_time'] == "closed") || ($_POST['saturday_start_time'] == "Close") || ($_POST['saturday_start_time'] == "close"))
{
    $_POST['saturday_start_time']    = "Closed";
    $_POST['saturday_end_time']      = "Closed";

    $_POST['saturday_start_time_he'] = "סגור";
    $_POST['saturday_end_time_he']   = "סגור";

    $sat_delivery_time = "Closed";
}
else
{
    $_POST['saturday_start_time_he'] = $_POST['saturday_start_time'];
    $_POST['saturday_end_time_he']   = $_POST['saturday_end_time'];

    $sat_end_time = $_POST['saturday_end_time'];

    $timestampsat = strtotime($sat_end_time) + 60*60;
    $sat_delivery_time = date('H:i', $timestampsat);
}




if(($_POST['sunday_start_time'] == "Closed") || ($_POST['sunday_start_time'] == "closed") || ($_POST['sunday_start_time'] == "Close") || ($_POST['sunday_start_time'] == "close"))
{
    $_POST['sunday_start_time']    = "Closed";
    $_POST['sunday_end_time']      = "Closed";

    $_POST['sunday_start_time_he'] = "סגור";
    $_POST['sunday_end_time_he']   = "סגור";


    $sun_delivery_time = "Closed";
}
else
{
    $_POST['sunday_start_time_he'] = $_POST['sunday_start_time'];
    $_POST['sunday_end_time_he']   = $_POST['sunday_end_time'];


    $sun_end_time = $_POST['sunday_end_time'];

    $timestampsun = strtotime($sun_end_time) + 60*60;
    $sun_delivery_time = date('H:i', $timestampsun);
}


$company_id = $_POST['company_id'];


$timings = getSpecificCompanyTiming($company_id);

$count = 1;
$week1[] ="";
$week2[] ="";
$week3[] ="";
$week4[] ="";
$week5[] ="";
$week6[] ="";
$week7[] ="";

foreach ($timings as $time)
{
    if($count == 1)
    {
        $week1['id']                    =  $time['id'];

    }
    if($count == 2)
    {
        $week2['id']                    =  $time['id'];

    }
    if($count == 3)
    {
        $week3['id']                    =  $time['id'];

    }
    if($count == 4)
    {
        $week4['id']                    =  $time['id'];

    }
    if($count == 5)
    {
        $week5['id']                    =  $time['id'];

    }
    if($count == 6)
    {
        $week6['id']                    =  $time['id'];

    }
    if($count == 7)
    {
        $week7['id']                    =   $time['id'];

    }
    $count++;

}


//GET THE OLD DISCOUNT OF COMPANY, IF THE DISCOUNT CHANGES, CHANGE ALL THE USERS DISCOUNT
DB::useDB(B2B_DB);
$old_discount  = DB::queryFirstRow("select discount from company where id = '$company_id'");
if($_POST['amount'] != $old_discount['discount'])
{
    //DISCOUNT IS CHANGED, UPDATE ALL USERS DISCOUNT
    DB::useDB(B2B_DB);
    DB::update('b2b_users', array(
        "discount"                          =>  $_POST['amount'],
    ),  "company_id=%d",    $company_id   );

}


//UPDATE COMPANY
DB::useDB(B2B_DB);
DB::update('company', array(
    "name"                                  =>  $_POST['name'],
    "registered_company_number"             =>  $_POST['registered_company_number'],
    "delivery_address"                      =>  $_POST['address'],
    "discount"                              =>  $_POST['amount'],
    "team_size"                             =>  $_POST['team_size'],
    "limit_of_restaurants"                  =>  $_POST['limit_of_restaurants'],
    "contact_name"                          =>  $_POST['contact_name'],
    "contact_number"                        =>  $_POST['contact_number'],
    "contact_email"                         =>  $_POST['contact_email'],
    "ledger_link"                           =>  $_POST['ledger_link'],
    "min_order"                             =>  $_POST['min_order'],
    "discount_type"                         =>  $_POST['discount_type'],
    "email"                                 =>  $_POST['email'],
    "password"                              =>  $_POST['password'],
    "notes"                                 =>  $_POST['notes'],
    "lat"                                   =>  $_POST['lat'],
    "lng"                                   =>  $_POST['lng'],

),  "id=%d",    $company_id   );


$week1_id = $week1['id'];
$week2_id = $week2['id'];
$week3_id = $week3['id'];
$week4_id = $week4['id'];
$week5_id = $week5['id'];
$week6_id = $week6['id'];
$week7_id = $week7['id'];

DB::useDB(B2B_DB);





$timings = DB::query("select * from company_timing where company_id = '$company_id'");


if(DB::count() == 0){



    DB::useDB(B2B_DB);
    DB::insert('company_timing', array(
        "company_id"                    =>  $company_id,
        "week_en"                       =>  "Monday",
        "week_he"                       =>  "יום ב",
        "opening_time"                  =>  $_POST['monday_start_time'],
        "closing_time"                  =>  $_POST['monday_end_time'],
        "opening_time_he"               =>  $_POST['monday_start_time_he'],
        "closing_time_he"               =>  $_POST['monday_end_time_he'],
        "delivery_timing"               =>  $mon_delivery_time
    ));

    DB::useDB(B2B_DB);
    DB::insert('company_timing', array(
        "company_id"                    =>  $company_id,
        "week_en"                       =>  "Tuesday",
        "week_he"                       =>  "יום ג",
        "opening_time"                  =>  $_POST['tuesday_start_time'],
        "closing_time"                  =>  $_POST['tuesday_end_time'],
        "opening_time_he"               =>  $_POST['tuesday_start_time_he'],
        "closing_time_he"               =>  $_POST['tuesday_end_time_he'],
        "delivery_timing"               =>  $tue_delivery_time

    ));
    DB::useDB(B2B_DB);
    DB::insert('company_timing', array(
        "company_id"                    =>  $company_id,
        "week_en"                       =>  "Wednesday",
        "week_he"                       =>  "יום ד",
        "opening_time"                  =>  $_POST['wednesday_start_time'],
        "closing_time"                  =>  $_POST['wednesday_end_time'],
        "opening_time_he"               =>  $_POST['wednesday_start_time_he'],
        "closing_time_he"               =>  $_POST['wednesday_end_time_he'],
        "delivery_timing"               =>  $wed_delivery_time
    ));

    DB::useDB(B2B_DB);
    DB::insert('company_timing', array(
        "company_id"                    =>  $company_id,
        "week_en"                       =>  "Thursday",
        "week_he"                       =>  "יום ה",
        "opening_time"                  =>  $_POST['thursday_start_time'],
        "closing_time"                  =>  $_POST['thursday_end_time'],
        "opening_time_he"               =>  $_POST['thursday_start_time_he'],
        "closing_time_he"               =>  $_POST['thursday_end_time_he'],
        "delivery_timing"               =>  $thur_delivery_time
    ));


    DB::useDB(B2B_DB);
    DB::insert('company_timing', array(
        "company_id"                    =>  $company_id,
        "week_en"                       =>  "Friday",
        "week_he"                       =>  "ששי",
        "opening_time"                  =>  $_POST['friday_start_time'],
        "closing_time"                  =>  $_POST['friday_end_time'],
        "opening_time_he"               =>  $_POST['friday_start_time_he'],
        "closing_time_he"               =>  $_POST['friday_end_time_he'],
        "delivery_timing"               =>  $fri_delivery_time
    ));


    DB::useDB(B2B_DB);
    DB::insert('company_timing', array(
        "company_id"                    =>  $company_id,
        "week_en"                       =>  "Saturday",
        "week_he"                       =>  "שבת",
        "opening_time"                  =>  $_POST['saturday_start_time'],
        "closing_time"                  =>  $_POST['saturday_end_time'],
        "opening_time_he"               =>  $_POST['saturday_start_time_he'],
        "closing_time_he"               =>  $_POST['saturday_end_time_he'],
        "delivery_timing"               =>  $sat_delivery_time
    ));


    DB::useDB(B2B_DB);
    DB::insert('company_timing', array(
        "company_id"                    =>  $company_id,
        "week_en"                       =>  "Sunday",
        "week_he"                       =>  "יום א",
        "opening_time"                  =>  $_POST['sunday_start_time'],
        "closing_time"                  =>  $_POST['sunday_end_time'],
        "opening_time_he"               =>  $_POST['sunday_start_time_he'],
        "closing_time_he"               =>  $_POST['sunday_end_time_he'],
        "delivery_timing"               =>  $sun_delivery_time
    ));


}
else{

    DB::update('company_timing', array(
    "opening_time"                  =>  $_POST['sunday_start_time'],
    "closing_time"                  =>  $_POST['sunday_end_time'],
    "opening_time_he"               =>  $_POST['sunday_start_time_he'],
    "closing_time_he"               =>  $_POST['sunday_end_time_he'],
        "delivery_timing"               =>  $sun_delivery_time

),  "id=%d",     $week7_id  );

    DB::useDB(B2B_DB);
DB::update('company_timing', array(
    "opening_time"                  =>  $_POST['monday_start_time'],
    "closing_time"                  =>  $_POST['monday_end_time'],
    "opening_time_he"               =>  $_POST['monday_start_time_he'],
    "closing_time_he"               =>  $_POST['monday_end_time_he'],
    "delivery_timing"               =>  $mon_delivery_time

),  "id=%d",     $week1_id  );

    DB::useDB(B2B_DB);
DB::update('company_timing', array(
    "opening_time"                  =>  $_POST['tuesday_start_time'],
    "closing_time"                  =>  $_POST['tuesday_end_time'],
    "opening_time_he"               =>  $_POST['tuesday_start_time_he'],
    "closing_time_he"               =>  $_POST['tuesday_end_time_he'],
    "delivery_timing"               =>  $tue_delivery_time

),  "id=%d",     $week2_id  );

    DB::useDB(B2B_DB);
DB::update('company_timing', array(
    "opening_time"                  =>  $_POST['wednesday_start_time'],
    "closing_time"                  =>  $_POST['wednesday_end_time'],
    "opening_time_he"               =>  $_POST['wednesday_start_time_he'],
    "closing_time_he"               =>  $_POST['wednesday_end_time_he'],
    "delivery_timing"               =>  $wed_delivery_time

),  "id=%d",     $week3_id  );

    DB::useDB(B2B_DB);
DB::update('company_timing', array(
    "opening_time"                  =>  $_POST['thursday_start_time'],
    "closing_time"                  =>  $_POST['thursday_end_time'],
    "opening_time_he"               =>  $_POST['thursday_start_time_he'],
    "closing_time_he"               =>  $_POST['thursday_end_time_he'],
    "delivery_timing"               =>  $thur_delivery_time

),  "id=%d",     $week4_id  );

    DB::useDB(B2B_DB);
DB::update('company_timing', array(
    "opening_time"                  =>  $_POST['friday_start_time'],
    "closing_time"                  =>  $_POST['friday_end_time'],
    "opening_time_he"               =>  $_POST['friday_start_time_he'],
    "closing_time_he"               =>  $_POST['friday_end_time_he'],
    "delivery_timing"               =>  $fri_delivery_time

),  "id=%d",     $week5_id  );

    DB::useDB(B2B_DB);
DB::update('company_timing', array(
    "opening_time"                  =>  $_POST['saturday_start_time'],
    "closing_time"                  =>  $_POST['saturday_end_time'],
    "opening_time_he"               =>  $_POST['saturday_start_time_he'],
    "closing_time_he"               =>  $_POST['saturday_end_time_he'],
    "delivery_timing"               =>  $sat_delivery_time

),  "id=%d",     $week6_id  );

}


$week_en = $_POST['week_en'];
$delivery_time = $_POST['delivery_time'];

DB::query("UPDATE company_timing set  delivery_timing = '$delivery_time' where company_id = '$company_id' and week_en = '$week_en' ");

echo json_encode($_POST['discount_type']);