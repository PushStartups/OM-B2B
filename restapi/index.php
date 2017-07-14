<?php

require      'vendor/autoload.php';
require      'PHPMailer/PHPMailerAutoload.php';
require_once 'inc/initDb.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Mailgun\Mailgun;


DB::query("set names utf8");


// ORDER RECEIVE EMAILS FOR SERVERS

// DEV SERVER
if($_SERVER['HTTP_HOST'] == "devb2b.orderapp.com")
{
    define("EMAIL","devb2borders@orderapp.com");
    define("B2BLINK","devb2b.orderapp.com");
}


// QA SERVER
else if($_SERVER['HTTP_HOST'] == "qab2b.orderapp.com"){

    define("EMAIL","qab2borders@orderapp.com");
    define("B2BLINK","qab2b.orderapp.com");
}


// PRODUCTION SERVER
else
{
    define("EMAIL","b2borders@orderapp.com");
    define("B2BLINK","b2b.orderapp.com");
}


// SERVER URL TO UPLOAD CONTENT



// SLIM INITIALIZATION
$app = new \Slim\App();




//  USER LOGIN FOR B2B
$app->post('/b2b_user_login', function ($request, $response, $args)
{
    try{

        $user_name = $request->getParam('user_name');
        $password  = $request->getParam('password');

        $obj      = '';
        $user     = '';
        $company  = '';

        DB::useDB('orderapp_b2b_wui');

        $userDB = DB::queryFirstRow("select * from b2b_users where user_name = '$user_name' and password = '$password'");


        if (DB::count() > 0)
        {

            $company_id         = $userDB['company_id'];
            $companyDB          = DB::queryFirstRow("select * from company where id = $company_id");


            $user['user_id']                =   $userDB['id'];
            $user['name']                   =   $userDB['name'];
            $user['email']                  =   $userDB['smooch_id'];
            $user['contact']                =   $userDB['contact'];
            $company['company_id']          =   $company_id;
            $company['company_name']        =   $companyDB['name'];
            $company['company_address']     =   $companyDB['delivery_address'];


            $obj['company']                 =   $company;
            $obj['user']                    =   $user;
            $obj['error']                   =   false;

        }
        else
        {
            $user = DB::queryFirstRow("select * from b2b_users where user_name = '$user_name'");

            $obj['error'] = true;

            if (DB::count() == 0) {

                $obj['field'] = "user-name";

            }
            else{

                $obj['field'] = "password";

            }

        }

        // RESPONSE RETURN TO REST API CALL
        $response = $response->withStatus(202);
        $response = $response->withJson(json_encode($obj));
        return $response;
    }

    catch(MeekroDBException $e) {


        $response =  $response->withStatus(500);
        $response =  $response->withHeader('Content-Type', 'text/html');
        $response =  $response->write( $e->getMessage());
        return $response;

    }


});



//  SEND CREDENTIAL DETAIL BACK TO USER IN CASE OF FORGET PASSWORD
$app->post('/forgot_email', function ($request, $response, $args){

    DB::useDB('orderapp_b2b_wui');

    $msg = '';
    $user_email = $request->getParam('email');


    $userLoginInfo = DB::queryFirstRow("select * from b2b_users WHERE smooch_id = %s",$user_email);


    if(DB::count() > 0)
    {

        $username = $userLoginInfo['user_name'];
        $password = $userLoginInfo['password'];

        $is_error = mailForgotPassword($password, $username, $user_email);

        ob_end_clean();

        $msg['error'] = false;

    }
    else
    {

        $msg['error'] = true;

    }

    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($msg));
    return $response;

});



//  SEND ERROR REPORT TO DEVELOPMENT TEAM
$app->post('/error_report', function ($request, $response, $args){


    DB::useDB('orderapp_b2b_wui');

    $msg = '';
    $host    =  $request->getParam('host');
    $url     =  $request->getParam('url');
    $message =  $request->getParam('message');


    sendReportToDevTeam($host,$url,$message);

    ob_end_clean();

    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode("true"));
    return $response;

});






// SAVE CATEGORY IMAGE FOR ADMIN PANEL UPDATE MAIN DB (ORDER APP)
$app->post('/save_category_image', function ($request, $response, $args)
{
    $resp = "";
    $data = '';

    $id = $request->getParam('cat_id');
    $menu_id = $request->getParam('menu_id');

    $rests = DB::queryFirstRow("select * from categories where id = '".$id."'");
    if(DB::count() != 0) {
        $data = $rests['image_url'];
    }
    $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));



    $menu_id = DB::queryFirstRow("select restaurant_id from menus where id = '".$menu_id."'");

    $restaurant = DB::queryFirstRow("select * from restaurants where id = '".$menu_id['restaurant_id']."'");


    $restaurant['name_en'] = preg_replace('/\s*/', '', $restaurant['name_en']);

    $restaurant['name_en'] = strtolower($restaurant['name_en']);


    $rests['name_en'] = preg_replace('/\s*/', '', $rests['name_en']);

    $rests['name_en'] = strtolower($rests['name_en']);



    //"/m/en/img/categories/".$restaurant['name_en']."/".$_POST['name_en'].".png"
    if(!is_dir("../m/en/img/categories/" . $restaurant['name_en']))
    {
        mkdir("../m/en/img/categories/" . $restaurant['name_en'], 0777);

    }


    $filepath = "../m/en/img/categories/".$restaurant['name_en']."/".$rests['name_en'].".png"; // or image.jpg


    $image_url = "";
    if(file_put_contents($filepath,$data))
    {
        $image_url = "/m/en/img/categories/".$restaurant['name_en']."/".$rests['name_en'].".png";
        $resp =  "working";

    }
    else{

        $resp = "not workign";
        $image_url = "/m/en/img/cs-category.png";
    }
    DB::query("update categories set image_url = '".$image_url."' where id = '$id'");

    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($resp));
    return $response;

});




// UPDATE DATA ENTERY DB

$app->post('/save_category_image_dataentry', function ($request, $response, $args)
{
    global $con;
    $resp = "";
    $data = '';

    $id = $request->getParam('cat_id');
    $menu_id = $request->getParam('menu_id');


    // CATEGORY IMAGE URL
    $get_brand = "select * from categories where id = '".$id."'";
    $run_brand = mysqli_query($con, $get_brand);

    while($row_brand = mysqli_fetch_array($run_brand))
    {
        $data = $row_brand['image_url'];
        $name  = $row_brand['name_en'];

    }

    $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));


    $get_brand = "select restaurant_id from menus where id = '".$menu_id."'";
    $run_brand = mysqli_query($con, $get_brand);

    while($row_brand2 = mysqli_fetch_array($run_brand))
    {
        $menu_id  = $row_brand2['restaurant_id'];

        $get_brand = "select * from restaurants where id = '".$menu_id."'";
        $run_brand = mysqli_query($con, $get_brand);

        while($row_brand1 = mysqli_fetch_array($run_brand))
        {
            //$data = $row_brand['image_url'];
            $restaurant['name_en']  = $row_brand1['name_en'];

        }


    }


    $restaurant['name_en'] = preg_replace('/\s*/', '', $restaurant['name_en']);

    $restaurant['name_en'] = strtolower($restaurant['name_en']);


    $rests['name_en'] = preg_replace('/\s*/', '', $name);

    $rests['name_en'] = strtolower($rests['name_en']);



    //"/m/en/img/categories/".$restaurant['name_en']."/".$_POST['name_en'].".png"
    if(!is_dir("../m/en/img/categories/" . $restaurant['name_en']))
    {
        mkdir("../m/en/img/categories/" . $restaurant['name_en'], 0777);

    }


    $filepath = "../m/en/img/categories/".$restaurant['name_en']."/".$rests['name_en'].".png"; // or image.jpg


    $image_url = "";
    if(file_put_contents($filepath,$data))
    {
        $image_url = "/m/en/img/categories/".$restaurant['name_en']."/".$rests['name_en'].".png";
        $resp =  "working";

    }
    else{

        $resp = "not workign";
        $image_url = "/m/en/img/cs-category.png";
    }

    $update_order = "update categories set image_url = '".$image_url."' where id = '$id'";
    $run_order = mysqli_query($con, $update_order);

    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($resp));
    return $response;

});




$app->post('/update_restaurant_logo', function ($request, $response, $args)
{
    $resp = "";
    $data = '';

    $id = $request->getParam('rest_id');

    $rests = DB::queryFirstRow("select * from restaurants where id = '".$id."'");

    if(DB::count() != 0) {

        $data = $rests['logo'];

    }

    $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));

    $name_logo = preg_replace('/\s*/', '', $rests['name_en']);

    $name_logo = strtolower($name_logo);

    if (!is_dir("/m/en/img"))
    {
        mkdir("/m/en/img", 0777);

    }

    $filepath = "/m/en/img/".$name_logo."_logo.png"; // or image.jpg


    $image_url = "";
    if(file_put_contents($filepath,$data))
    {
        $image_url = "/m/en/img/".$name_logo."_logo.png";
        $resp =  "workingg";

    }
    else
    {

        $resp = "not working";
        $image_url = "/m/en/img/cs-logo.png";

    }
    DB::query("update restaurants set logo = '".$image_url."' where id = '$id'");




    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($image_url));
    return $response;

});



$app->post('/insert_new_restaurant_dataentry', function ($request, $response, $args)
{


    global $con;

    $resp = "";
    $data = '';

    $id = $request->getParam('rest_id');



    $get_brand = "select * from restaurants where id = '".$id."'";
    $run_brand = mysqli_query($con, $get_brand);

    while($row_brand = mysqli_fetch_array($run_brand))
    {
        $data = $row_brand['logo'];
        $name  = $row_brand['name_en'];

    }


    $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));

    $name_logo = preg_replace('/\s*/', '', $name);

    $name_logo = strtolower($name_logo);

    $filepath = "../m/en/img/".$name_logo."_logo.png"; // or image.jpg


    $image_url = "";
    if(file_put_contents($filepath,$data))
    {
        $image_url = "/m/en/img/".$name_logo."_logo.png";
        $resp =  "workingg";

    }
    else
    {

        $resp = "not working";
        $image_url = "/m/en/img/cs-logo.png";

    }

    $update_order = "update restaurants set logo = '".$image_url."' where id = '$id'";
    $run_order = mysqli_query($con, $update_order);


    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($resp));
    return $response;

});



$app->post('/insert_new_restaurant', function ($request, $response, $args)
{
    $resp = "";
    $data = '';

    $id = $request->getParam('rest_id');

    $rests = DB::queryFirstRow("select * from restaurants where id = '".$id."'");

    if(DB::count() != 0) {

        $data = $rests['logo'];

    }

    $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));

    $name_logo = preg_replace('/\s*/', '', $rests['name_en']);

    $name_logo = strtolower($name_logo);

    $filepath = "../m/en/img/".$name_logo."_logo.png"; // or image.jpg


    $image_url = "";
    if(file_put_contents($filepath,$data))
    {
        $image_url = "/m/en/img/".$name_logo."_logo.png";
        $resp =  "workingg";

    }
    else
    {

        $resp = "not working";
        $image_url = "/m/en/img/cs-logo.png";

    }
    DB::query("update restaurants set logo = '".$image_url."' where id = '$id'");




    $response = $response->withStatus(202);
    $response = $response->withJson(json_encode($resp));
    return $response;

});



$app->run();



//GENERATE EMAIL FORGET PASSWORD USER CRENDENTIALS
function mailForgotPassword($password, $username, $user_email){

    $mailbody = '<!DOCTYPE html>';
    $mailbody .= '<html lang="en">';
    $mailbody .= '<head>';
    $mailbody .= '<meta charset="UTF-8">';
    $mailbody .= '<title>Email Template</title>';
    $mailbody .= '<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">';
    $mailbody .= '</head>';
    $mailbody .= '<body style="background:#fff; font-family:\'Open Sans\',sans-serif;">';
    $mailbody .= '<style>';
    $mailbody .= '@import url(https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800);';
    $mailbody .= '</style>';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" align="center" height="100%" width="500" style="text-align: left; overflow:hidden; background-color:#fff; color: #000; border: 1px solid #cacaca; font-size: 15px; line-height: 18px; font-weight: 400;">';
    $mailbody .= '<tr><td align="center" valign="top">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="500">';
    $mailbody .= '<tr><td align="center" valign="top" style="padding: 20px 15px; background:#ff7f00;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="100%" id="emailHeader">';
    $mailbody .= '<tr><td align="center" valign="top">';
    $mailbody .= '<h1 style="text-align: left; margin: 0 0 5px; color: #fff; font-size: 20px; line-height: 23px; font-weight: 700;">Password recovery</h1>';
    $mailbody .= '<p style="text-align: left; margin: 0; color: #fff;">'.date("Y/m/d").'</p></td>';
    $mailbody .= '<td align="center" valign="top" style="text-align: right; width: 52px;"><img style="display: block;" src="https://'.$_SERVER['HTTP_HOST'].'/restapi/images/delivery-email.png"></td></tr>';
    $mailbody .= '<tr><td align="center" valign="top"></td></tr></table></td></tr>';
    $mailbody .= '<tr><td align="center" valign="top" style="padding: 30px 15px 10px;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="100%">';
    $mailbody .= '<tr><td align="center" valign="top" style="text-align: left; width: 100px; font-weight: 400;">';
    $mailbody .= '<p style="margin:0;">Your account is active and you may login from our b2b Ordering page, Account details are given below</p></td></tr></table></td></tr>';
    $mailbody .= '<tr><td align="center" valign="top" style="padding: 10px 15px;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="100%">';
    $mailbody .= '<tr><td align="center" valign="top" style="text-align: left; width: 100px; font-weight: 700;">';
    $mailbody .= 'User Name:';
    $mailbody .= '</td><td align="center" valign="top" style="text-align: left;">';
    $mailbody .= $username;
    $mailbody .= '</td></tr></table></td></tr>';
    $mailbody .= '<tr><td align="center" valign="top" style="padding: 10px 15px 30px;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr>';
    $mailbody .= '<td align="center" valign="top" style="text-align: left; width: 100px; font-weight: 700;">';
    $mailbody .= 'Password:';
    $mailbody .= '</td><td align="center" valign="top" style="text-align: left;">';
    $mailbody .= $password;
    $mailbody .= '</td></tr></table>	';
    $mailbody .= '</td></tr><tr>';
    $mailbody .= '<td align="center" valign="top" style="padding: 10px 15px; background: #ffae5e;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="100%">';
    $mailbody .= '<tr><td align="center" valign="top" style="text-align: left; width: 100px; font-weight: 700;">';
    $mailbody .= '<a style="display: block; width: 87px; margin: 0 auto; color: #fff;" href="'.$_SERVER['HTTP_HOST'].'"><img style="display: block; margin: 0 auto;" src="https://'.$_SERVER['HTTP_HOST'].'/restapi/images/logo-image.png"></a></td></tr></table>';
    $mailbody .= '</td></tr></table></td></tr></table>';
    $mailbody .= '</body>';
    $mailbody .= '</html>';




    $mail = new PHPMailer;
    $mail->CharSet = 'UTF-8';
    $mail->SMTPDebug = 3;                                               // Enable verbose debug output

    $mail->isSMTP();
    $mail->Host = "email-smtp.eu-west-1.amazonaws.com";                 //   Set mailer to use SMTP

    $mail->SMTPAuth = true;                                             //   Enable SMTP authentication
    $mail->Username = "AKIAJZTPZAMJBYRSJ27A";
    $mail->Password = "AujjPinHpYPuio4CYc5LgkBrSRbs++g9sJIjDpS4l2Ky";   //   SMTP password
    $mail->SMTPSecure = 'tls';                                          //   Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;

    //From email address and name
    $mail->From = "orders@orderapp.com";
    $mail->FromName = "OrderApp";


    //To address and name
    $mail->addAddress($user_email);                      // SEND EMAIL TO USER

    $mail->AddCC(EMAIL);                        //SEND  CLIENT EMAIL COPY TO ADMIN

    //Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = 'Password Recovery';
    $mail->Body =    $mailbody;
    $mail->AltBody = "OrderApp";


    if ($mail->send())
    {
        $msg = "Message has been sent successfully";
        return false;

    }
    else
    {

        $msg = "Mailer Error: " . $mail->ErrorInfo;
        return true;

    }

}


//SEND REPORT TO DEV TEAM ERRORS
function sendReportToDevTeam($host, $url, $message){

    $mailbody = '<!DOCTYPE html>';
    $mailbody .= '<html lang="en">';
    $mailbody .= '<head>';
    $mailbody .= '<meta charset="UTF-8">';
    $mailbody .= '<title>Email Template</title>';
    $mailbody .= '<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">';
    $mailbody .= '</head>';
    $mailbody .= '<body style="background:#fff; font-family:\'Open Sans\',sans-serif;">';
    $mailbody .= '<style>';
    $mailbody .= '@import url(https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800);';
    $mailbody .= '</style>';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" align="center" height="100%" width="500" style="text-align: left; overflow:hidden; background-color:#fff; color: #000; border: 1px solid #cacaca; font-size: 15px; line-height: 18px; font-weight: 400;">';
    $mailbody .= '<tr><td align="center" valign="top">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="500">';
    $mailbody .= '<tr><td align="center" valign="top" style="padding: 20px 15px; background:#ff7f00;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="100%" id="emailHeader">';
    $mailbody .= '<tr><td align="center" valign="top">';
    $mailbody .= '<h1 style="text-align: left; margin: 0 0 5px; color: #fff; font-size: 20px; line-height: 23px; font-weight: 700;">Password recovery</h1>';
    $mailbody .= '<p style="text-align: left; margin: 0; color: #fff;">'.date("Y/m/d").'</p></td>';
    $mailbody .= '<td align="center" valign="top" style="text-align: right; width: 52px;"><img style="display: block;" src="https://'.$_SERVER['HTTP_HOST'].'/restapi/images/delivery-email.png"></td></tr>';
    $mailbody .= '<tr><td align="center" valign="top"></td></tr></table></td></tr>';
    $mailbody .= '<tr><td align="center" valign="top" style="padding: 30px 15px 10px;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="100%">';
    $mailbody .= '<tr><td align="center" valign="top" style="text-align: left; width: 100px; font-weight: 400;">';
    $mailbody .= '<p style="margin:0;">Error occurs on system, details are given below </p></td></tr></table></td></tr>';
    $mailbody .= '<tr><td align="center" valign="top" style="padding: 10px 15px;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="100%">';
    $mailbody .= '<tr><td align="center" valign="top" style="text-align: left; width: 100px; font-weight: 700;">';
    $mailbody .= 'HOST NAME :';
    $mailbody .= '</td><td align="center" valign="top" style="text-align: left;">';
    $mailbody .= $host;
    $mailbody .= '</td></tr></table></td></tr>';
    $mailbody .= '<tr><td align="center" valign="top" style="padding: 10px 15px 10px;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr>';
    $mailbody .= '<td align="center" valign="top" style="text-align: left; width: 100px; font-weight: 700;">';
    $mailbody .= 'URL :';
    $mailbody .= '</td><td align="center" valign="top" style="text-align: left;">';
    $mailbody .= $url;
    $mailbody .= '</td></tr></table>	';
    $mailbody .= '</td></tr><tr>';

    $mailbody .= '<tr><td align="center" valign="top" style="padding: 10px 15px 30px;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr>';
    $mailbody .= '<td align="center" valign="top" style="text-align: left; width: 100px; font-weight: 700;">';
    $mailbody .= 'MESSAGE :';
    $mailbody .= '</td><td align="center" valign="top" style="text-align: left;">';
    $mailbody .= $message;
    $mailbody .= '</td></tr></table>	';
    $mailbody .= '</td></tr><tr>';

    $mailbody .= '<td align="center" valign="top" style="padding: 10px 15px; background: #ffae5e;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="100%">';
    $mailbody .= '<tr><td align="center" valign="top" style="text-align: left; width: 100px; font-weight: 700;">';
    $mailbody .= '<a style="display: block; width: 87px; margin: 0 auto; color: #fff;" href="'.$_SERVER['HTTP_HOST'].'"><img style="display: block; margin: 0 auto;" src="https://'.$_SERVER['HTTP_HOST'].'/restapi/images/logo-image.png"></a></td></tr></table>';
    $mailbody .= '</td></tr></table></td></tr></table>';
    $mailbody .= '</body>';
    $mailbody .= '</html>';




    $mail = new PHPMailer;
    $mail->CharSet = 'UTF-8';
    $mail->SMTPDebug = 3;                                                 // Enable verbose debug output

    $mail->isSMTP();
    $mail->Host = "email-smtp.eu-west-1.amazonaws.com";                   //   Set mailer to use SMTP

    $mail->SMTPAuth = true;                                               //   Enable SMTP authentication
    $mail->Username = "AKIAJZTPZAMJBYRSJ27A";
    $mail->Password = "AujjPinHpYPuio4CYc5LgkBrSRbs++g9sJIjDpS4l2Ky";     //   SMTP password
    $mail->SMTPSecure = 'tls';                                            //   Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;

    //From email address and name
    $mail->From = "orders@orderapp.com";
    $mail->FromName = "OrderApp";


    //To address and name
    $mail->addAddress('iftikhar@experintsol.com');                // SEND EMAIL TO DEV TEAM

    $mail->AddCC(EMAIL);                                          //SEND  CLIENT EMAIL COPY TO ADMIN

    //Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = 'Password Recovery';
    $mail->Body =    $mailbody;
    $mail->AltBody = "OrderApp";


    if ($mail->send())
    {
        $msg = "Message has been sent successfully";
        return false;

    }
    else
    {

        $msg = "Mailer Error: " . $mail->ErrorInfo;
        return true;

    }

}