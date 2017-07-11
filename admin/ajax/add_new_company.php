<?php
require_once '../inc/initDb.php';
DB::query("set names utf8");


DB::useDB('orderapp_b2b_wui');

DB::insert('company', array(
    "name"              =>  $_POST['name'],
    "registered_company_number"              =>  $_POST['registered_company_number'],
    "delivery_address"  =>  $_POST['address'],
    "min_order"         =>  $_POST['min_order'],
    "discount"          =>  $_POST['amount'],
    "discount_type"     =>  $_POST['discount_type'],

    "payment_method"     =>  $_POST['payment_method'],
    "team_size"     =>  $_POST['team_size'],
    "ordering_deadline_time"     =>  $_POST['ordering_deadline_time'],
    "delivery_time"     =>  $_POST['delivery_time'],
    "limit_of_restaurants"     =>  $_POST['limit_of_restaurants'],
    "company_address"     =>  $_POST['company_address'],
    "contact_name"     =>  $_POST['contact_name'],
    "contact_number"     =>  $_POST['contact_number'],
    "contact_email"     =>  $_POST['contact_email'],
    "ledger_link"     =>  $_POST['ledger_link'],

    "email"             =>  $_POST['email'],
    "password"          =>  $_POST['password'],
    "notes"          =>  $_POST['notes'],
    "voting"            =>  0,
    "lat"          =>  $_POST['lat'],
    "lng"          =>  $_POST['lng'],
));

$company_id  = DB::insertId();

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





DB::useDB('orderapp_b2b_wui');
DB::insert('company_timing', array(
    "company_id"                    =>  $company_id,
    "week_en"                       =>  "Monday",
    "week_he"                       =>  "יום ב",
    "opening_time"                  =>  $_POST['monday_start_time'],
    "closing_time"                  =>  $_POST['monday_end_time'],
    "opening_time_he"               =>  $_POST['monday_start_time_he'],
    "closing_time_he"               =>  $_POST['monday_end_time_he']
));

DB::useDB('orderapp_b2b_wui');
DB::insert('company_timing', array(
    "company_id"                    =>  $company_id,
    "week_en"                       =>  "Tuesday",
    "week_he"                       =>  "יום ג",
    "opening_time"                  =>  $_POST['tuesday_start_time'],
    "closing_time"                  =>  $_POST['tuesday_end_time'],
    "opening_time_he"               =>  $_POST['tuesday_start_time_he'],
    "closing_time_he"               =>  $_POST['tuesday_end_time_he']

));
DB::useDB('orderapp_b2b_wui');
DB::insert('company_timing', array(
    "company_id"                    =>  $company_id,
    "week_en"                       =>  "Wednesday",
    "week_he"                       =>  "יום ד",
    "opening_time"                  =>  $_POST['wednesday_start_time'],
    "closing_time"                  =>  $_POST['wednesday_end_time'],
    "opening_time_he"               =>  $_POST['wednesday_start_time_he'],
    "closing_time_he"               =>  $_POST['wednesday_end_time_he']
));

DB::useDB('orderapp_b2b_wui');
DB::insert('company_timing', array(
    "company_id"                    =>  $company_id,
    "week_en"                       =>  "Thursday",
    "week_he"                       =>  "יום ה",
    "opening_time"                  =>  $_POST['thursday_start_time'],
    "closing_time"                  =>  $_POST['thursday_end_time'],
    "opening_time_he"               =>  $_POST['thursday_start_time_he'],
    "closing_time_he"               =>  $_POST['thursday_end_time_he']
));


DB::useDB('orderapp_b2b_wui');
DB::insert('company_timing', array(
    "company_id"                    =>  $company_id,
    "week_en"                       =>  "Friday",
    "week_he"                       =>  "ששי",
    "opening_time"                  =>  $_POST['friday_start_time'],
    "closing_time"                  =>  $_POST['friday_end_time'],
    "opening_time_he"               =>  $_POST['friday_start_time_he'],
    "closing_time_he"               =>  $_POST['friday_end_time_he']
));


DB::useDB('orderapp_b2b_wui');
DB::insert('company_timing', array(
    "company_id"                    =>  $company_id,
    "week_en"                       =>  "Saturday",
    "week_he"                       =>  "שבת",
    "opening_time"                  =>  $_POST['saturday_start_time'],
    "closing_time"                  =>  $_POST['saturday_end_time'],
    "opening_time_he"               =>  $_POST['saturday_start_time_he'],
    "closing_time_he"               =>  $_POST['saturday_end_time_he']
));


DB::useDB('orderapp_b2b_wui');
DB::insert('company_timing', array(
    "company_id"                    =>  $company_id,
    "week_en"                       =>  "Sunday",
    "week_he"                       =>  "יום א",
    "opening_time"                  =>  $_POST['sunday_start_time'],
    "closing_time"                  =>  $_POST['sunday_end_time'],
    "opening_time_he"               =>  $_POST['sunday_start_time_he'],
    "closing_time_he"               =>  $_POST['sunday_end_time_he']
));


echo json_encode("success");