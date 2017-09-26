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



$TEST_MODE = false;
$companies = getCompanies($TEST_MODE);
echo '<pre>'; var_dump($companies); echo '</pre>';
sendMessages($companies, $TEST_MODE);



?>