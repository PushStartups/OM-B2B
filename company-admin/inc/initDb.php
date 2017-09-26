<?php
//session_set_cookie_params(0, '/', '.itpvt.net');


require_once 'meekrodb.2.3.class.php';

if($_SERVER['HTTP_HOST'] == "b2b.orderapp.com" || $_SERVER['HTTP_HOST'] == "stagingb2b.orderapp.com" || $_SERVER['HTTP_HOST'] == "backupb2b.orderapp.com" ) {


    DB::$host = 'pushxx-oappxxx-prodxxxx.crv4lzhgi1gx.eu-central-1.rds.amazonaws.com';
    DB::$port = '3306';
}

DB::$user = 'root';
DB::$password = 'RDSx!_OAPPxxx#';
DB::$dbName = 'orderapp_restaurants_b2b_wui';



?>
