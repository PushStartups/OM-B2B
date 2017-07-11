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
}
else
{
    $_POST['monday_start_time_he'] = $_POST['monday_start_time'];
    $_POST['monday_end_time_he']   = $_POST['monday_end_time'];
}


if(($_POST['tuesday_start_time'] == "Closed") || ($_POST['tuesday_start_time'] == "closed") || ($_POST['tuesday_start_time'] == "Close") || ($_POST['tuesday_start_time'] == "close"))
{
    $_POST['tuesday_start_time']    = "Closed";
    $_POST['tuesday_end_time']      = "Closed";

    $_POST['tuesday_start_time_he'] = "סגור";
    $_POST['tuesday_end_time_he']   = "סגור";
}
else
{
    $_POST['tuesday_start_time_he'] = $_POST['tuesday_start_time'];
    $_POST['tuesday_end_time_he']   = $_POST['tuesday_end_time'];
}




if(($_POST['wednesday_start_time'] == "Closed") || ($_POST['wednesday_start_time'] == "closed") || ($_POST['wednesday_start_time'] == "Close") || ($_POST['wednesday_start_time'] == "close"))
{
    $_POST['wednesday_start_time']    = "Closed";
    $_POST['wednesday_end_time']      = "Closed";

    $_POST['wednesday_start_time_he'] = "סגור";
    $_POST['wednesday_end_time_he']   = "סגור";
}
else
{
    $_POST['wednesday_start_time_he'] = $_POST['wednesday_start_time'];
    $_POST['wednesday_end_time_he']   = $_POST['wednesday_end_time'];
}




if(($_POST['thursday_start_time'] == "Closed") || ($_POST['thursday_start_time'] == "closed") || ($_POST['thursday_start_time'] == "Close") || ($_POST['thursday_start_time'] == "close"))
{
    $_POST['thursday_start_time']    = "Closed";
    $_POST['thursday_end_time']      = "Closed";

    $_POST['thursday_start_time_he'] = "סגור";
    $_POST['thursday_end_time_he']   = "סגור";
}
else
{
    $_POST['thursday_start_time_he'] = $_POST['thursday_start_time'];
    $_POST['thursday_end_time_he']   = $_POST['thursday_end_time'];
}




if(($_POST['friday_start_time'] == "Closed") || ($_POST['friday_start_time'] == "closed") || ($_POST['friday_start_time'] == "Close") || ($_POST['friday_start_time'] == "close"))
{
    $_POST['friday_start_time']    = "Closed";
    $_POST['friday_end_time']      = "Closed";

    $_POST['friday_start_time_he'] = "סגור";
    $_POST['friday_end_time_he']   = "סגור";
}
else
{
    $_POST['friday_start_time_he'] = $_POST['friday_start_time'];
    $_POST['friday_end_time_he']   = $_POST['friday_end_time'];
}




if(($_POST['saturday_start_time'] == "Closed") || ($_POST['saturday_start_time'] == "closed") || ($_POST['saturday_start_time'] == "Close") || ($_POST['saturday_start_time'] == "close"))
{
    $_POST['saturday_start_time']    = "Closed";
    $_POST['saturday_end_time']      = "Closed";

    $_POST['saturday_start_time_he'] = "סגור";
    $_POST['saturday_end_time_he']   = "סגור";
}
else
{
    $_POST['saturday_start_time_he'] = $_POST['saturday_start_time'];
    $_POST['saturday_end_time_he']   = $_POST['saturday_end_time'];
}




if(($_POST['sunday_start_time'] == "Closed") || ($_POST['sunday_start_time'] == "closed") || ($_POST['sunday_start_time'] == "Close") || ($_POST['sunday_start_time'] == "close"))
{
    $_POST['sunday_start_time']    = "Closed";
    $_POST['sunday_end_time']      = "Closed";

    $_POST['sunday_start_time_he'] = "סגור";
    $_POST['sunday_end_time_he']   = "סגור";
}
else
{
    $_POST['sunday_start_time_he'] = $_POST['sunday_start_time'];
    $_POST['sunday_end_time_he']   = $_POST['sunday_end_time'];
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








DB::useDB('orderapp_b2b');


//UPDATE COMPANY
DB::update('company', array(
    "name"              =>  $_POST['name'],
    "delivery_address"  =>  $_POST['address'],
    "discount"          =>  $_POST['amount'],
    "min_order"          =>  $_POST['min_order'],
    "discount_type"     =>  $_POST['discount_type'],
    "email"             =>  $_POST['email'],
    "password"          =>  $_POST['password'],
    "lat"          =>  $_POST['lat'],
    "lng"          =>  $_POST['lng'],

),  "id=%d",    $company_id   );


$week1_id = $week1['id'];
$week2_id = $week2['id'];
$week3_id = $week3['id'];
$week4_id = $week4['id'];
$week5_id = $week5['id'];
$week6_id = $week6['id'];
$week7_id = $week7['id'];

DB::useDB('orderapp_b2b');
DB::update('company_timing', array(
    "opening_time"                  =>  $_POST['sunday_start_time'],
    "closing_time"                  =>  $_POST['sunday_end_time'],
    "opening_time_he"               =>  $_POST['sunday_start_time_he'],
    "closing_time_he"               =>  $_POST['sunday_end_time_he']

),  "id=%d",     $week7_id  );

DB::useDB('orderapp_b2b');
DB::update('company_timing', array(
    "opening_time"                  =>  $_POST['monday_start_time'],
    "closing_time"                  =>  $_POST['monday_end_time'],
    "opening_time_he"               =>  $_POST['monday_start_time_he'],
    "closing_time_he"               =>  $_POST['monday_end_time_he']

),  "id=%d",     $week1_id  );

DB::useDB('orderapp_b2b');
DB::update('company_timing', array(
    "opening_time"                  =>  $_POST['tuesday_start_time'],
    "closing_time"                  =>  $_POST['tuesday_end_time'],
    "opening_time_he"               =>  $_POST['tuesday_start_time_he'],
    "closing_time_he"               =>  $_POST['tuesday_end_time_he']

),  "id=%d",     $week2_id  );

DB::useDB('orderapp_b2b');
DB::update('company_timing', array(
    "opening_time"                  =>  $_POST['wednesday_start_time'],
    "closing_time"                  =>  $_POST['wednesday_end_time'],
    "opening_time_he"               =>  $_POST['wednesday_start_time_he'],
    "closing_time_he"               =>  $_POST['wednesday_end_time_he']

),  "id=%d",     $week3_id  );

DB::useDB('orderapp_b2b');
DB::update('company_timing', array(
    "opening_time"                  =>  $_POST['thursday_start_time'],
    "closing_time"                  =>  $_POST['thursday_end_time'],
    "opening_time_he"               =>  $_POST['thursday_start_time_he'],
    "closing_time_he"               =>  $_POST['thursday_end_time_he']

),  "id=%d",     $week4_id  );

DB::useDB('orderapp_b2b');
DB::update('company_timing', array(
    "opening_time"                  =>  $_POST['friday_start_time'],
    "closing_time"                  =>  $_POST['friday_end_time'],
    "opening_time_he"               =>  $_POST['friday_start_time_he'],
    "closing_time_he"               =>  $_POST['friday_end_time_he']

),  "id=%d",     $week5_id  );

DB::useDB('orderapp_b2b');
DB::update('company_timing', array(
    "opening_time"                  =>  $_POST['saturday_start_time'],
    "closing_time"                  =>  $_POST['saturday_end_time'],
    "opening_time_he"               =>  $_POST['saturday_start_time_he'],
    "closing_time_he"               =>  $_POST['saturday_end_time_he']

),  "id=%d",     $week6_id  );







echo json_encode($_POST['discount_type']);