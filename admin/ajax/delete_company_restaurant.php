<?php
require_once '../inc/initDb.php';
$restaurant_id = $_POST['rest_id'];
$company_id = $_POST['company_id'];

DB::useDB(B2B_DB);

DB::query("delete from company_rest where company_id = '$company_id' and rest_id = '$restaurant_id' ");

echo "success";