<?php
session_start();

$card_no        =   $_POST['card_no'];
$cvv            =   $_POST['cvv'];
$expiry_year    =   $_POST['expiry_year'];
$expiry_month   =   $_POST['expiry_month'];

//echo $card_no." ".$cvv." ".$expiry_year." ".$expiry_month." ".$_SESSION['email'];

$service_url = $_SERVER['HTTP_HOST'].'/restapi/index.php/store_credit_card_info';
$curl = curl_init($service_url);
$curl_post_data = array(
    "card_no"       =>      $card_no,
    "cvv"           =>      $cvv,
    "expiry"        =>      $expiry_month.$expiry_year,
    "user_email"    =>      $_SESSION['email']
);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
$curl_response = curl_exec($curl);
curl_close($curl);

$arr    = json_decode($curl_response,true);
//$arr    =   $curl_response;



if($arr['success'] == 1)
{
    echo json_encode("success");
}
else
{
    echo json_encode("false");
}


//echo json_encode("success");