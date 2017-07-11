<?php
require_once '../inc/initDb.php';
session_start();
$email = $_POST['email'];

$admin = DB::queryFirstRow("select * from b2b_users where smooch_id = '$email'");
if(DB::count() > 0)
{
    $password = $admin['password'];
    $service_url = $_SERVER['HTTP_HOST'].'/restapi/index.php/forget_email_to_b2b_users';
    $curl = curl_init($service_url);
    $curl_post_data = array(
        "email"     => $email,
        "password"  => $password
    );
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
    $curl_response = curl_exec($curl);
    curl_close($curl);

    echo json_encode($admin['id']);

}
else{
    echo json_encode("false");
}





