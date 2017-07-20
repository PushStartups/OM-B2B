<?php
require_once '../inc/initDb.php';
$email = $_POST['email'];
$company_id = $_POST['company_id'];

DB::useDB(B2B_DB);

DB::query("delete from b2b_users where smooch_id = '$email' and company_id = '$company_id' ");

echo "success";