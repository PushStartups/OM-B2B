<?php
require_once '../inc/initDb.php';
$delivery_id = $_POST['delivery_id'];

DB::query("delete from delivery_fee where  id = '$delivery_id' ");

echo "success";