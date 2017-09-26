<?php
require_once '../inc/initDb.php';
$rest_id = $_POST['rest_id'];

DB::useDB('orderapp_b2b');

DB::query("delete from b2b_rest_discounts where  id = '$rest_id' ");

echo json_encode('success') ;