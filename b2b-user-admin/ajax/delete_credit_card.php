<?php
require_once '../inc/initDb.php';
session_start();
DB::useDB('orderapp_b2b');
DB::delete('user_credit_cards', "id=%d", $_POST['id']);

echo json_encode("success");