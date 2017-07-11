<?php
require_once '../inc/initDb.php';


$company_id      =       $_POST['company_id'];


DB::useDB('orderapp_b2b');


DB::delete('delivery_timings', "company_id=%d", $company_id );

DB::delete('b2b_orders', "company_id=%d", $company_id );

DB::delete('b2b_rest_discounts', "company_id=%d", $company_id );

DB::delete('b2b_users', "company_id=%d", $company_id );

DB::delete('vote_timings', "company_id=%d", $company_id );

DB::delete('company_voting', "company_id=%d", $company_id );

DB::delete('company_timing', "company_id=%d", $company_id );

DB::delete('company_rest', "company_id=%d", $company_id );

DB::delete('company', "id=%d", $company_id );

echo json_encode("success");