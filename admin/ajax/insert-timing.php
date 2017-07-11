<?php
require_once '../inc/initDb.php';
require_once '../inc/functions.php';
session_start();
DB::query("set names utf8");



if(($_POST['sunday_start_time'] == "Closed") || ($_POST['sunday_start_time'] == "closed") )
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



if(($_POST['monday_start_time'] == "Closed") || ($_POST['monday_start_time'] == "closed") )
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


if(($_POST['tuesday_start_time'] == "Closed") || ($_POST['tuesday_start_time'] == "closed") )
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




if(($_POST['wednesday_start_time'] == "Closed") || ($_POST['wednesday_start_time'] == "closed") )
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




if(($_POST['thursday_start_time'] == "Closed") || ($_POST['thursday_start_time'] == "closed"))
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




if(($_POST['friday_start_time'] == "Closed") || ($_POST['friday_start_time'] == "closed") )
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




if(($_POST['saturday_start_time'] == "Closed") || ($_POST['saturday_start_time'] == "closed") )
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



$restaurant_id = $_POST['restaurant_id'];

$timings = DB::query("select * from weekly_availibility where restaurant_id = '$restaurant_id'");


if(DB::count() == 0){


    DB::insert('weekly_availibility', array(
        "restaurant_id"                 =>  $_POST['restaurant_id'],
        "week_en"                       =>  "Sunday",
        "week_he"                       =>  "יום א",
        "opening_time"                  =>  $_POST['sunday_start_time'],
        "closing_time"                  =>  $_POST['sunday_end_time'],
        "opening_time_he"               =>  $_POST['sunday_start_time_he'],
        "closing_time_he"               =>  $_POST['sunday_end_time_he']
    ));


    DB::insert('weekly_availibility', array(
        "restaurant_id"                 =>  $_POST['restaurant_id'],
        "week_en"                       =>  "Monday",
        "week_he"                       =>  "יום ב",
        "opening_time"                  =>  $_POST['monday_start_time'],
        "closing_time"                  =>  $_POST['monday_end_time'],
        "opening_time_he"               =>  $_POST['monday_start_time_he'],
        "closing_time_he"               =>  $_POST['monday_end_time_he']
    ));


    DB::insert('weekly_availibility', array(
        "restaurant_id"                 =>  $_POST['restaurant_id'],
        "week_en"                       =>  "Tuesday",
        "week_he"                       =>  "יום ג",
        "opening_time"                  =>  $_POST['tuesday_start_time'],
        "closing_time"                  =>  $_POST['tuesday_end_time'],
        "opening_time_he"               =>  $_POST['tuesday_start_time_he'],
        "closing_time_he"               =>  $_POST['tuesday_end_time_he']

    ));

    DB::insert('weekly_availibility', array(
        "restaurant_id"                 =>  $_POST['restaurant_id'],
        "week_en"                       =>  "Wednesday",
        "week_he"                       =>  "יום ד",
        "opening_time"                  =>  $_POST['wednesday_start_time'],
        "closing_time"                  =>  $_POST['wednesday_end_time'],
        "opening_time_he"               =>  $_POST['wednesday_start_time_he'],
        "closing_time_he"               =>  $_POST['wednesday_end_time_he']
    ));


    DB::insert('weekly_availibility', array(
        "restaurant_id"                 =>  $_POST['restaurant_id'],
        "week_en"                       =>  "Thursday",
        "week_he"                       =>  "יום ה",
        "opening_time"                  =>  $_POST['thursday_start_time'],
        "closing_time"                  =>  $_POST['thursday_end_time'],
        "opening_time_he"               =>  $_POST['thursday_start_time_he'],
        "closing_time_he"               =>  $_POST['thursday_end_time_he']
    ));



    DB::insert('weekly_availibility', array(
        "restaurant_id"                 =>  $_POST['restaurant_id'],
        "week_en"                       =>  "Friday",
        "week_he"                       =>  "ששי",
        "opening_time"                  =>  $_POST['friday_start_time'],
        "closing_time"                  =>  $_POST['friday_end_time'],
        "opening_time_he"               =>  $_POST['friday_start_time_he'],
        "closing_time_he"               =>  $_POST['friday_end_time_he']
    ));



    DB::insert('weekly_availibility', array(
        "restaurant_id"                 =>  $_POST['restaurant_id'],
        "week_en"                       =>  "Saturday",
        "week_he"                       =>  "שבת",
        "opening_time"                  =>  $_POST['saturday_start_time'],
        "closing_time"                  =>  $_POST['saturday_end_time'],
        "opening_time_he"               =>  $_POST['saturday_start_time_he'],
        "closing_time_he"               =>  $_POST['saturday_end_time_he']
    ));


}

else {

    $timings = getAllTimings($restaurant_id);

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
            $week1['opening_time']          =   $time['opening_time'];
            $week1['closing_time']          =   $time['closing_time'];
        }
        if($count == 2)
        {
            $week2['id']                    =  $time['id'];
            $week2['opening_time']          =   $time['opening_time'];
            $week2['closing_time']          =   $time['closing_time'];
        }
        if($count == 3)
        {
            $week3['id']                    =  $time['id'];
            $week3['opening_time']          =   $time['opening_time'];
            $week3['closing_time']          =   $time['closing_time'];
        }
        if($count == 4)
        {
            $week4['id']                    =  $time['id'];
            $week4['opening_time']          =   $time['opening_time'];
            $week4['closing_time']          =   $time['closing_time'];
        }
        if($count == 5)
        {
            $week5['id']                    =  $time['id'];
            $week5['opening_time']          =   $time['opening_time'];
            $week5['closing_time']          =   $time['closing_time'];
        }
        if($count == 6)
        {
            $week6['id']                    =  $time['id'];
            $week6['opening_time']          =   $time['opening_time'];
            $week6['closing_time']          =   $time['closing_time'];
        }
        if($count == 7)
        {
            $week7['id']                    =  $time['id'];
            $week7['opening_time']          =   $time['opening_time'];
            $week7['closing_time']          =   $time['closing_time'];
        }
        $count++;

    }




    $week1_id = $week1['id'] ;
    $week2_id = $week2['id'] ;
    $week3_id = $week3['id'] ;
    $week4_id = $week4['id'] ;
    $week5_id = $week5['id'] ;
    $week6_id = $week6['id'] ;
    $week7_id = $week7['id'] ;


    DB::update('weekly_availibility', array(
        "opening_time"                  =>  $_POST['sunday_start_time'],
        "closing_time"                  =>  $_POST['sunday_end_time'],
        "opening_time_he"               =>  $_POST['sunday_start_time_he'],
        "closing_time_he"               =>  $_POST['sunday_end_time_he']

    ),  "id=%d",     $week1_id  );


    DB::update('weekly_availibility', array(
        "opening_time"                  =>  $_POST['monday_start_time'],
        "closing_time"                  =>  $_POST['monday_end_time'],
        "opening_time_he"               =>  $_POST['monday_start_time_he'],
        "closing_time_he"               =>  $_POST['monday_end_time_he']

    ),  "id=%d",     $week2_id  );


    DB::update('weekly_availibility', array(
        "opening_time"                  =>  $_POST['tuesday_start_time'],
        "closing_time"                  =>  $_POST['tuesday_end_time'],
        "opening_time_he"               =>  $_POST['tuesday_start_time_he'],
        "closing_time_he"               =>  $_POST['tuesday_end_time_he']

    ),  "id=%d",     $week3_id  );


    DB::update('weekly_availibility', array(
        "opening_time"                  =>  $_POST['wednesday_start_time'],
        "closing_time"                  =>  $_POST['wednesday_end_time'],
        "opening_time_he"               =>  $_POST['wednesday_start_time_he'],
        "closing_time_he"               =>  $_POST['wednesday_end_time_he']

    ),  "id=%d",     $week4_id  );


    DB::update('weekly_availibility', array(
        "opening_time"                  =>  $_POST['thursday_start_time'],
        "closing_time"                  =>  $_POST['thursday_end_time'],
        "opening_time_he"               =>  $_POST['thursday_start_time_he'],
        "closing_time_he"               =>  $_POST['thursday_end_time_he']

    ),  "id=%d",     $week5_id  );


    DB::update('weekly_availibility', array(
        "opening_time"                  =>  $_POST['friday_start_time'],
        "closing_time"                  =>  $_POST['friday_end_time'],
        "opening_time_he"               =>  $_POST['friday_start_time_he'],
        "closing_time_he"               =>  $_POST['friday_end_time_he']

    ),  "id=%d",     $week6_id  );


    DB::update('weekly_availibility', array(
        "opening_time"                  =>  $_POST['saturday_start_time'],
        "closing_time"                  =>  $_POST['saturday_end_time'],
        "opening_time_he"               =>  $_POST['saturday_start_time_he'],
        "closing_time_he"               =>  $_POST['saturday_end_time_he']

    ),  "id=%d",     $week7_id  );




}


echo json_encode("success");