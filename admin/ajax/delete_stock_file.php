<?php
require_once '../inc/initDb.php';

DB::useDB(B2B_DB);

$id            =  $_POST['id'];
DB::query("delete from stock_invoice_taxing_pdf where  id = '$id' ");

echo json_encode("success");