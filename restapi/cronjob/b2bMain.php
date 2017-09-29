<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once 'functions.php';
//require_once (dirname(__FILE__) . '/../PHPMailer/PHPMailerAutoload.php');
//require_once (dirname(__FILE__) . '/../Interfax/vendor/autoload.php');
require_once (dirname(__FILE__) . '/../inc/initDb.php');



date_default_timezone_set('Asia/Jerusalem');

//use Mailgun\Mailgun;
//use Interfax\Client;
DB::query("set names utf8");

DB::useDB('orderapp_b2b_wui');

//GET ALL THE COMPANIES THAT A SCHEDULED TO FINISH DOING THEIR ORDERS
$companies = getCompanies(false);

//SET TEST MODE FOR COMPANY WITH ID=2
//TEST_MODE = TRUE MAKE sendMessage TO WORK IN TEST PROTOCOL
$TEST_MODE = false;
if ($companies[0]["id"] == 2) {
  $TEST_MODE = true;
}
echo '<pre>'; var_dump($companies); echo '</pre>';

//TAKES THE COMPANY ONE BY ONE AND SENDS MESSAGES TO EVERY RESTAURANT THIS COMPANY MADE ORDERS FROM
sendMessages($companies, $TEST_MODE);



?>