<?php
require_once '../inc/initDb.php';

session_start();
DB::query("set names utf8");


$company_id = $_POST['company_id'];
$email = $_POST['smooch_id'];
$password = $_POST['name'].rand(100,9999);
$username = strtolower($_POST['name']);
DB::useDB(B2B_DB);
$company = DB::queryFirstRow("SELECT * FROM company where id = '$company_id'");
$discount = $company['discount'];

DB::useDB(B2B_DB);

DB::insert('b2b_users', array(

    "company_id"        => $company_id,
    "smooch_id"         => $email,
    "name"              => $_POST['name'],
    "password"          => $password,
    "contact"           => $_POST['contact'],
    "address"           => $_POST['address'],
    "language"          => "english",
    "voucherify_id"     => 0,
    "user_name"         => $username,
    "discount"          => $discount,
    "date"              => DB::sqleval("NOW()")

));

echo json_encode("success");


$service_url = $_SERVER['HTTP_HOST'].'/restapi/index.php/send_email_to_b2b_users';
$curl = curl_init($service_url);
$curl_post_data = array(
    "email"     => $email,
    "password"  => $password,
    "user_name" => $username
);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
$curl_response = curl_exec($curl);
curl_close($curl);