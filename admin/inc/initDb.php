<?php
//session_set_cookie_params(0, '/', '.itpvt.net');


require_once 'meekrodb.2.3.class.php';
if($_SERVER['HTTP_HOST'] == "qab2b.orderapp.com") {

    DB::$host = 'production-orderapp.crv4lzhgi1gx.eu-central-1.rds.amazonaws.com';
    DB::$port = '3306';
}


DB::$user = 'root';
DB::$password = 'orderapp';

DB::$dbName = 'orderapp_restaurants_b2b_wui';

define("B2B_DB","orderapp_b2b_wui");
define("B2B_RESTAURANTS","orderapp_restaurants_b2b_wui");

?>
