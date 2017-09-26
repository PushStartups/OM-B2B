<?php
require_once '../inc/initDb.php';


$company_id      =       $_POST['company_id'];


DB::useDB(B2B_DB);


DB::delete('b2b_orders', "company_id=%d", $company_id );

DB::delete('b2b_rest_discounts', "company_id=%d", $company_id );

DB::delete('b2b_users', "company_id=%d", $company_id );


DB::delete('company_timing', "company_id=%d", $company_id );

DB::delete('company_rest', "company_id=%d", $company_id );

DB::delete('company', "id=%d", $company_id );

echo json_encode("success");