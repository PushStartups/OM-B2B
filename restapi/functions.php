<?php


// EMAIL ORDER SUMMARY ENGLISH VERSION
function email_order_summary_english($user_order,$orderId,$todayDate)
{

    $mailbody  = '<html><head></head>';
    $mailbody .= '<body style="padding: 0; margin: 0" >';
    $mailbody .= '<div style="max-width: 600px; margin: 0 auto; border: 1px solid #D3D3D3; border-radius: 5px; overflow: hidden " >';
    $mailbody .= '<div style="font-family: Open Sans" src="https://fonts.googleapis.com/css?family=Open+Sans:300">';
    $mailbody .= '<div  style="background-image: url(http://dev.orderapp.com/restapi/images/header.png); background-repeat: no-repeat; background-position: center; background-size: cover;">';
    $mailbody .= '<table style="width: 100%; color: white; padding: 30px" >';
    $mailbody .= '<tr style="font-size: 30px; padding: 10px" >';
    $mailbody .= '<td > <img style="padding-top: 10px; width: 20px" src="http://dev.orderapp.com/restapi/images/bag.png" > Order Summary </td>';
    $mailbody .= '<td style="text-align: right">'.$user_order['total_paid'].' NIS</td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 10px" >';
    $mailbody .= '<td> '.$todayDate.' &nbsp; Order ID # '.$orderId.'</td>';
    $mailbody .= '<td style="text-align: right">'.$user_order['payment_option'].'</td>';
    $mailbody .= '</tr>';
    $mailbody .= '</table>';
    $mailbody .= '</div>';
    $mailbody .= '<div  style="padding: 10px 30px 0px 30px;" >';

    foreach($user_order['rests_orders'][0]['foodCartData'] as $t) {

        $mailbody .= '<table style="width: 100%; color:black; padding: 30px 0; border-bottom: 1px solid #D3D3D3" >';

        $mailbody .= '<tr style="font-size: 18px; padding: 10px; font-weight: bold" >';
        // print item name
        $mailbody .= '<td >' . $t['name'] . '  </td>';
        $mailbody .= '<td style="text-align: right; white-space: nowrap"> '.$t['price'].' NIS X '.$t['qty'].'  &nbsp; <span style="color: FF864C;" >'.(($t['price'] * $t['qty'])).' NIS</span></td>';
        $mailbody .= '</tr>';

        // subitems
        if($t['specialRequest'] != "") {

            if ($t['detail'] != '') {

                $mailbody .= '<td >' . $t['detail'] .', Special Request : '.$t['specialRequest']. '</td>';
            }
            else
            {

                $mailbody .= '<td >' . $t['detail'].' Special Request : '.$t['specialRequest'].' </td>';
            }
        }
        else
        {
            $mailbody .= '<td >' . $t['detail'] . ' </td>';
        }
        $mailbody .= '<td style="text-align: right"> </td>';
        $mailbody .= '</tr>';

        $mailbody .= '</table>';

    }

    $mailbody .= '</div>';


    $mailbody .= '<table style="width: 100%; color:black; padding:10px 30px; background: #FEF2E8; border-bottom: 1px solid #D3D3D3 " >';

    if($user_order['isCoupon'] == "false")
    {
        $mailbody .= '<tr style="font-size: 18px;  font-weight: bold" >';
        $mailbody .= '<td style="padding: 5px 0" > Total </td>';
        $mailbody .= '<td style="text-align: right; white-space: nowrap"> <span style="color: #FF864C;" >'.$user_order['total_paid'].' NIS</span></td>';
        $mailbody .= '</tr>';


    }
    else
    {
        $mailbody .= '<tr style="font-size: 18px;  font-weight: bold" >';
        $mailbody .= '<td style="padding: 5px 0" > Sub total  </td>';
        $mailbody .= '<td style="text-align: right; white-space: nowrap"> <span style="color: #FF864C;" >'.$user_order['actual_total'].' NIS</span></td>';
        $mailbody .= '</tr>';

    }

    //TODAY REMAINING BALANCE SECTION
    $mailbody .= '<tr style="font-size: 18px;  font-weight: bold" >';
    if($user_order['discount_type'] == "daily"){
        $mailbody .= "<td style='padding: 5px 0' >Remaining Balance Today* </td>";
    }
    else{
        $mailbody .= "<td style='padding: 5px 0' >Remaining Balance for the month* </td>";
    }

    $mailbody .= '<td style="text-align: right; white-space: nowrap"> <span style="color: #FF864C;" > '.$user_order['discount'].' NIS</span></td>';
    $mailbody .= '</tr>';



    $mailbody .= '</table>';

    if($user_order['specialRequest'] != '')
    {

        $mailbody .= '<br><span style="color: #000000; padding:10px 30px;">Special Request : <span style="color: #808080">'.$user_order["specialRequest"].'</span></span><br>';

    }


    $mailbody .= '<table style=" color:black; padding:10px 30px; width: 100%; " cellspacing="5px">';
    $mailbody .= '<tr style="font-size: 18px;  font-weight: bold" >';
    $mailbody .= '<td colspan="2" style="padding: 10px 0" > Customer information   </td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
    $mailbody .= '<td style="padding: 10px 0" > <img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_user.png" ></td>';
    $mailbody .= '<td style="text-align: left; white-space: nowrap"> '.$user_order['user']['name'].' </td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
    $mailbody .= '<td style="padding: 10px 0" > <img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_phone.png" ></td>';
    $mailbody .= '<td style="text-align: left; white-space: nowrap"> '.$user_order['user']['contact'].' </td>';
    $mailbody .= '</tr>';
    //COMPANY INFO
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
    $mailbody .= '<td style="padding: 10px 0" > <img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_company.png" ></td>';
    $mailbody .= '<td style="text-align: left; white-space: nowrap"> '.$user_order['company']['company_name'].' </td>';
    $mailbody .= '</tr>';
    //COMPANY INFO ENDS

    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
    $mailbody .= '<td style="padding: 10px 0; text-align: center" > <img style=" height: 24px" src="http://dev.orderapp.com/restapi/images/ic_location.png" ></td>';

    $mailbody .= '<td style="text-align: left; white-space: nowrap"> Deliver At : '.$user_order['company']['company_address'].'</td>';



    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
    $mailbody .= '<td style="padding: 10px 0" > <img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_email.png" ></td>';
    $mailbody .= '<td style="text-align: left; white-space: nowrap"> '.$user_order['user']['email'].' </td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
    $mailbody .= '<td style="padding: 10px 0" > <img style=" width: 20px" src="http://dev.orderapp.com/restapi/images/ic_card.png" ></td>';
    $mailbody .= '<td style="text-align: left; white-space: nowrap"> '.$user_order['payment_option'].' </td>';
    $mailbody .= '</tr>';
    $mailbody .= '</table>';
    $mailbody .= '<h4 style="padding: 5px 27px;">* Use by end of '.$user_order['company']['company_name'].' orderding time</h4>';

    $mailbody .= '</div>';
    $mailbody .= '</div>';
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
    $mail->addAddress($user_order['user']['email']);     // SEND EMAIL TO USER
    $mail->AddCC(EMAIL);
    $mail->AddCC("brina@orderapp.com");
    $mail->AddBCC("oded@orderapp.com");


//Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = 'Biz '.$user_order['rests_orders'][0]['selectedRestaurant']['name_en'].' Order# '.$orderId;
    $mail->Body = $mailbody;
    $mail->AltBody = "OrderApp";

    if (!$mail->send())
    {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
    else
    {
        echo "Message has been sent successfully";
    }

}



// EMAIL ORDER SUMMARY ENGLISH VERSION
function email_order_summary_english_cancel($user_order,$orderId,$todayDate,$remaining_discount)
{

    $mailbody  = '<html><head></head>';
    $mailbody .= '<body style="padding: 0; margin: 0" >';
    $mailbody .= '<div style="max-width: 600px; margin: 0 auto; border: 1px solid #D3D3D3; border-radius: 5px; overflow: hidden " >';
    $mailbody .= '<div style="font-family: Open Sans" src="https://fonts.googleapis.com/css?family=Open+Sans:300">';
    $mailbody .= '<div  style="background-image: url(http://dev.orderapp.com/restapi/images/header.png); background-repeat: no-repeat; background-position: center; background-size: cover;">';
    $mailbody .= '<table style="width: 100%; color: white; padding: 30px" >';
    $mailbody .= '<tr style="font-size: 30px; padding: 10px" >';
    $mailbody .= '<td > <img style="padding-top: 10px; width: 20px" src="http://dev.orderapp.com/restapi/images/bag.png" >Cancel Order Summary </td>';
    $mailbody .= '<td style="text-align: right"> -'.$user_order->total_paid.' NIS</td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 10px" >';
    $mailbody .= '<td> '.$todayDate.' &nbsp; Order ID # '.$orderId.'</td>';
    $mailbody .= '<td style="text-align: right">'.$user_order->payment_option.'</td>';
    $mailbody .= '</tr>';
    $mailbody .= '</table>';
    $mailbody .= '</div>';
    $mailbody .= '<div  style="padding: 10px 30px 0px 30px;" >';
    foreach($user_order->rests_orders[0]->foodCartData as $t)
    {

        $mailbody .= '<table style="width: 100%; color:black; padding: 30px 0; border-bottom: 1px solid #D3D3D3" >';

        $mailbody .= '<tr style="font-size: 18px; padding: 10px; font-weight: bold" >';
        // print item name
        $mailbody .= '<td >' . $t->name . '  </td>';
        $mailbody .= '<td style="text-align: right; white-space: nowrap"> -'.$t->price.' NIS X '.$t->qty.'  &nbsp; <span style="color: FF864C;" > -'.(($t->price * $t->qty)).' NIS</span></td>';
        $mailbody .= '</tr>';

        //subitems
        if($t->specialRequest != "") {

            if ($t->detail != '') {

                $mailbody .= '<td >' . $t->detail .', Special Request : '.$t->specialRequest. '</td>';
            }
            else
            {

                $mailbody .= '<td >' . $t->detail.' Special Request : '.$t->specialRequest.' </td>';
            }
        }
        else
        {
            $mailbody .= '<td >' . $t->detail . ' </td>';
        }
        $mailbody .= '<td style="text-align: right"> </td>';
        $mailbody .= '</tr>';

        $mailbody .= '</table>';

    }

    $mailbody .= '</div>';



    $mailbody .= '<table style="width: 100%; color:black; padding:10px 30px; background: #FEF2E8; border-bottom: 1px solid #D3D3D3 " >';

    if($user_order->isCoupon == "false")
    {
        $mailbody .= '<tr style="font-size: 18px;  font-weight: bold" >';
        $mailbody .= '<td style="padding: 5px 0" > Total </td>';
        $mailbody .= '<td style="text-align: right; white-space: nowrap"> <span style="color: #FF864C;" >-'.$user_order->total_paid.' NIS</span></td>';
        $mailbody .= '</tr>';


    }
    else
    {
        $mailbody .= '<tr style="font-size: 18px;  font-weight: bold" >';
        $mailbody .= '<td style="padding: 5px 0" > Sub total  </td>';
        $mailbody .= '<td style="text-align: right; white-space: nowrap"> <span style="color: #FF864C;" >-'.$user_order->actual_total.' NIS</span></td>';
        $mailbody .= '</tr>';

    }

    //TODAY REMAINING BALANCE SECTION
    $mailbody .= '<tr style="font-size: 18px;  font-weight: bold" >';
    if($user_order->discount_type == "daily"){
        $mailbody .= "<td style='padding: 5px 0' >Remaining Balance Today* </td>";
    }
    else{
        $mailbody .= "<td style='padding: 5px 0' >Remaining Balance for the month* </td>";
    }

    $mailbody .= '<td style="text-align: right; white-space: nowrap"> <span style="color: #FF864C;" > '.$remaining_discount.' NIS</span></td>';
    $mailbody .= '</tr>';



    $mailbody .= '</table>';

    if($user_order->specialRequest != '')
    {

        $mailbody .= '<br><span style="color: #000000; padding:10px 30px;">Special Request : <span style="color: #808080">'.$user_order->specialRequest.'</span></span><br>';

    }


    $mailbody .= '<table style=" color:black; padding:10px 30px; width: 100%; " cellspacing="5px">';
    $mailbody .= '<tr style="font-size: 18px;  font-weight: bold" >';
    $mailbody .= '<td colspan="2" style="padding: 10px 0" > Customer information   </td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
    $mailbody .= '<td style="padding: 10px 0" > <img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_user.png" ></td>';
    $mailbody .= '<td style="text-align: left; white-space: nowrap"> '.$user_order->user->name.' </td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
    $mailbody .= '<td style="padding: 10px 0" > <img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_phone.png" ></td>';
    $mailbody .= '<td style="text-align: left; white-space: nowrap"> '.$user_order->user->contact.' </td>';
    $mailbody .= '</tr>';
    //COMPANY INFO
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
    $mailbody .= '<td style="padding: 10px 0" > <img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_company.png" ></td>';
    $mailbody .= '<td style="text-align: left; white-space: nowrap"> '.$user_order->company->company_name.' </td>';
    $mailbody .= '</tr>';
    //COMPANY INFO ENDS

    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
    $mailbody .= '<td style="padding: 10px 0; text-align: center" > <img style=" height: 24px" src="http://dev.orderapp.com/restapi/images/ic_location.png" ></td>';

    $mailbody .= '<td style="text-align: left; white-space: nowrap"> Deliver At : '.$user_order->company->company_address.'</td>';



    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
    $mailbody .= '<td style="padding: 10px 0" > <img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_email.png" ></td>';
    $mailbody .= '<td style="text-align: left; white-space: nowrap"> '.$user_order->user->email.' </td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
    $mailbody .= '<td style="padding: 10px 0" > <img style=" width: 20px" src="http://dev.orderapp.com/restapi/images/ic_card.png" ></td>';
    $mailbody .= '<td style="text-align: left; white-space: nowrap"> '.$user_order->payment_option.' </td>';
    $mailbody .= '</tr>';
    $mailbody .= '</table>';
    $mailbody .= '<h4 style="padding: 5px 27px;">* Use by end of '.$user_order->company->company_name.' orderding time</h4>';

    $mailbody .= '</div>';
    $mailbody .= '</div>';
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
    $mail->addAddress($user_order->user->email);     // SEND EMAIL TO USER
    $mail->AddCC(EMAIL);
    $mail->AddCC("brina@orderapp.com");
    $mail->AddBCC("oded@orderapp.com");


//Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = 'Cancel Order : Biz '.$user_order->rests_orders[0]->selectedRestaurant->name_en.' Order# '.$orderId;
    $mail->Body = $mailbody;
    $mail->AltBody = "OrderApp";

    if (!$mail->send())
    {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
    else
    {
        echo "Message has been sent successfully";
    }


}


// ADMIN EMAIL
// EMAIL ORDER SUMMARY HEBREW VERSION FOR ADMIN
function email_for_kitchen_cancel($user_order,$orderId,$todayDate)
{
    $mailbody = '';

    // USER NAME
    $mailbody .=  $user_order['user']['name'].' : שֵׁם';
    $mailbody .= '<br>';
    $mailbody .= '<br>';


    $mailbody .=  $user_order['company']['company_name'].' : שם החברה';
    $mailbody .= '<br>';
    $mailbody .= '<br>';


    $mailbody .= ' :  הזמנה';

    foreach($user_order['rests_orders'][0]['foodCartData']  as $t)
    {

        $mailbody .= '<br>';
        $mailbody .= '<br>';


        $mailbody.= $t['name_he'];

        $mailbody .= '<br>';
        $mailbody .= '<br>';

        $mailbody .=  $t['detail_he'];

        $mailbody .= '<br>';
        $mailbody .= '<br>';

        if($t['specialRequest'] != "") {


            if ($t['detail_he'] != '')
            {

                $mailbody .= $t['specialRequest'].' : בקשה מיוחדת';

            }
            else {

                $mailbody .= $t['specialRequest'].' : בקשה מיוחדת';
            }


        }

        $mailbody .= '<br>';
        $mailbody .= '<br>';
        $mailbody .= '<br>';
        $mailbody .= '<br>';

    }


    $mailbody .= $user_order['actual_total'].' : סך כל החשבון ללא דיסקונט';
    $mailbody .= '<br>';
    $mailbody .= '<br>';


    $mailbody .= $user_order['total_paid'].' : סה"כ';
    $mailbody .= '<br>';
    $mailbody .= '<br>';



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
    $mail->addAddress(EMAIL);                    //SEND ADMIN EMAIL


    //Address to which recipient will reply
    $mail->addReplyTo("orders@orderapp.com", "Reply");


    //Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = " הזמנה חדשה ".substr($user_order['user']['contact'], -4) . " #" . $user_order['rests_orders'][0]['selectedRestaurant']['name_he'];
    $mail->Body = $mailbody;
    $mail->AltBody = "OrderApp";

    if (!$mail->send()) {

        echo "Mailer Error: " . $mail->ErrorInfo;

    }
    else {

        echo "Message has been sent successfully";

    }

}

function traccer($order_id,$name,$phone,$start_address,$delivery_address,$startLat,$startLng,$endLat,$endLng)
{
    $service_url = "http://35.156.74.68:8082/api/objectives";
    $curl = curl_init($service_url);
    $curl_post_data = array(

        "name"           => $name,
        "phone"          => $phone,
        "startLatitude"  => $startLat,
        "startLongitude" => $startLng,
        "endLatitude"    => $endLat,
        "endLongitude"   => $endLng,
        "deviceId"       => 100,
        "status"         => "incomplete",
        "startAddress"   => $start_address,
        "endAddress"     => $delivery_address,
        "orderId"        => $order_id,
        "geocode"        => "no",
        "timeCreate"     => null

    );


    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_USERPWD,  "admin:admin");
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($curl_post_data));
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Authorization: Basic YWRtaW46YWRtaW4=',
        'Content-Type: application/json'
    ));


    $curl_response = curl_exec($curl);
    curl_close($curl);


    return $curl_response;

}



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
    $mailbody .= '<h1 style="text-align: left; margin: 0 0 5px; color: #fff; font-size: 20px; line-height: 23px; font-weight: 700;">OrderApp Errors</h1>';
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
    $mail->addAddress('errors@orderapp.com');                // SEND EMAIL TO DEV TEAM
    $mail->addAddress('ahmad@experintsol.com');                // SEND EMAIL TO DEV TEAM
    $mail->addAddress('errors@experintsol.com');                // SEND EMAIL TO DEV TEAM

    $mail->AddCC(EMAIL);                                          //SEND  CLIENT EMAIL COPY TO ADMIN

    //Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = 'OrderApp Errors';
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


// EMAIL ORDER SUMMARY HEBREW VERSION
function email_order_summary_hebrew($user_order,$orderId,$todayDate)
{

    $mailbody  = '<html><head><meta charset="UTF-8"></head>';
    $mailbody  .= '<body style="padding: 0; margin: 0" >';
    $mailbody  .= '<div style="max-width: 600px; margin: 0 auto; border: 1px solid #D3D3D3; border-radius: 5px; overflow: hidden ">';
    $mailbody  .= '<style>';
    $mailbody  .= '@import url("https://fonts.googleapis.com/css?family=Open+Sans:300");';
    $mailbody  .= '</style>';
    $mailbody  .= '<div style="font-family: Open Sans">';
    $mailbody  .= '<div style="background-image: url(http://dev.orderapp.com/restapi/images/header.png); background-repeat: no-repeat; background-position: center; background-size: cover;" >';
    $mailbody  .= '<table style="width: 100%; color: white; padding: 30px">';
    $mailbody  .= '<tr style="font-size: 30px; padding: 10px">';
    $mailbody  .= '<td dir="rtl" style="text-align: left">'.$user_order['total'].' ש"ח'.'</td>';
    $mailbody  .= '<td style="text-align: right;" >  סיכום הזמנה <img style="padding-top: 10px; width: 20px" src="http://dev.orderapp.com/restapi/images/bag.png" ></td>';
    $mailbody  .= '</tr>';
    $mailbody  .= '<tr style="font-size: 12px; padding: 10px" >';
    $mailbody  .= '<td>'.$user_order['Cash_Card_he'].'</td>';
    $mailbody  .= '<td style="text-align: right" dir="rtl">';
    $mailbody  .=  '&nbsp;'.$todayDate.'&nbsp;';
    $mailbody  .= 'מספר הזמנה #';
    $mailbody  .=  $orderId;
    $mailbody  .= '</tr>';
    $mailbody  .= '</table>';
    $mailbody  .= '</div>';
    $mailbody  .= '<div  style="padding: 10px 30px 0px 30px;" >';

    foreach($user_order['cartData'] as $t) {

        $mailbody.='<table style="width: 100%; color:black; padding: 30px 0; border-bottom: 1px solid #D3D3D3" >';
        $mailbody.='<tr style="font-size: 18px; padding: 10px; font-weight: bold" >';
        $mailbody.='<span style="color: #FF864C;" dir="rtl">';
        $mailbody.=(($t['price'] * $t['qty'])). ' ש"ח';
        $mailbody.='</span> &nbsp; <span dir="rtl">  ש"ח</span>';
        $mailbody.=$t['price'].' x '.$t['qty'].'</td>';
        $mailbody.='<td style="text-align: right;" >'. $t['name_he'] .'</td>';
        $mailbody.='</tr>';
        $mailbody.='<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
        $mailbody.='<td > </td>';
        $mailbody.='<td style="text-align: right; padding: 5px" dir="rtl">'.$t['detail_he'].'</td>';

        if($t['specialRequest'] != "") {

            if ($t['detail_he'] == '') {


                $mailbody.='<td style="text-align: right; padding: 5px" dir="rtl">'.$t['detail_he'].' הערות : '.$t['specialRequest'].'</td>';

            }
            else {

                $mailbody.='<td style="text-align: right; padding: 5px" dir="rtl">'.$t['detail_he'].', הערות : '.$t['specialRequest'].'</td>';

            }
        }
        else
        {
            $mailbody.='<td style="text-align: right; padding: 5px" dir="rtl">'.$t['detail_he'].'</td>';

        }

        $mailbody.='</tr>';
        $mailbody.='</table>';
    }

    $mailbody .= '</div>';
    $mailbody .= '<table style="width: 100%; color:black; padding:10px 30px; background: #FEF2E8; border-bottom: 1px solid #D3D3D3 ">';

    if($user_order['isCoupon'] == "false")
    {

        $mailbody .= '<tr style="font-size: 18px;  font-weight: bold">';
        $mailbody .= '<td style=" white-space: nowrap"> <span style="color: #FF864C;" >&nbsp;<span dir="rtl">ש"ח</span>&nbsp;'.$user_order['total']. '</span></td>';
        $mailbody .= '<td style="padding: 5px 0; text-align: right; " > סה"כ </td>';
        $mailbody .= '</tr>';

    }
    else
    {
        $mailbody .= '<tr style="font-size: 18px;  font-weight: bold">';
        $mailbody .= '<td style=" white-space: nowrap"> <span style="color: #FF864C;" >&nbsp;<span dir="rtl">ש"ח</span>&nbsp;'.$user_order['totalWithoutDiscount'].'</span></td>';
        $mailbody .= '<td style="padding: 5px 0; text-align: right; " > סיכום ביניים </td>';
        $mailbody .= '</tr>';


    }

    //TODAY REMAINING BALANCE SECTION
    $mailbody .= '<tr style="font-size: 18px;  font-weight: bold" >';
    $mailbody .= '<td style="white-space: nowrap"> <span style="color: #FF864C;" >&nbsp;<span dir="rtl">ש"ח</span>&nbsp; '.$user_order['discount'].'</span></td>';
    if($user_order['discount_type'] == "daily"){
        $mailbody .= '<td style="padding: 5px 0; text-align: right;" >יתרת היום* </td>';
    }
    else{
        $mailbody .= '<td style="padding: 5px 0; text-align: right;" >יתרה חודשית* </td>';
    }

    $mailbody .= '</tr>';


    $mailbody .= '</table>';
    if($user_order['specialRequest'] != '')
    {

        $mailbody .= '<br><span style="color: #000000;text-align: right;float: right;" dir="rtl"> <span style="color: #808080; padding:10px 30px;">בקשה מיוחדת :</span>'.$user_order["specialRequest"].'</span><br>';

    }


    $mailbody .= '<table style="float: right;color:black; padding:10px 30px; width: 100%; position: relative; left: calc(100% - 270px)" cellspacing="5px">';
    $mailbody .= '<tr style="font-size: 18px;  font-weight: bold" >';
    $mailbody .= '<td colspan="2" style="padding: 10px 0; text-align: right" dir="rtl" > מידע ללקוחות   </td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';
    $mailbody .= '<td style="text-align: right; white-space: nowrap"> '.$user_order['contact'].' </td>';
    $mailbody .= '<td style="padding: 10px 0"><img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_phone.png"></td>';
    $mailbody .= '</tr>';

    //COMPANY INFO
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';
    $mailbody .= '<td style="text-align: right; white-space: nowrap"> '.$user_order['company_name'].' </td>';
    $mailbody .= '<td style="padding: 10px 0"><img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_company.png"></td>';
    $mailbody .= '</tr>';


    //COMPANY INFO ENDS


    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';

    //COMPANY ADDRESS
    $mailbody .= '<td style="text-align: right; white-space: nowrap" dir="rtl"> לספק ב : '.$user_order['delivery_address'].'</td>';



    $mailbody .=  '<td style="padding: 10px 0; text-align: center"> <img style="height: 24px" src="http://dev.orderapp.com/restapi/images/ic_location.png" ></td>';
    $mailbody .=  '</tr>';
    $mailbody .=  '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';
    $mailbody .=  '<td style="text-align: right; white-space: nowrap">'.$user_order['email'].'</td>';
    $mailbody .=  '<td style="padding: 10px 0;"><img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_email.png" ></td>';
    $mailbody .=  '</tr>';
    $mailbody .=  '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';
    $mailbody .=  '<td style="text-align: right; white-space: nowrap">'.$user_order['Cash_Card_he'].'</td>';
    $mailbody .=  '<td style="padding: 10px 0;" > <img style=" width: 20px" src="http://dev.orderapp.com/restapi/images/ic_card.png" ></td>';
    $mailbody .=  '</tr>';
    //COMMENT SECTION
    $mailbody .=  '<tr style="font-size: 13px; padding: 5px 10px; color: #000; font-weight: bold;">';
    $mailbody .=  '<td style="text-align: right;">  יש להשתמש ביתרה עד סוף זמן ההזמנה של בית העסק *</td>';

    $mailbody .=  '</tr>';
    $mailbody .=  '</table>';

    $mailbody .=  '</div></div></body></html>';


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
    $mail->addAddress($user_order['email']);     // SEND EMAIL TO USER
    $mail->addAddress(EMAIL);
    $mail->AddCC("brina@orderapp.com");
    //SEND  CLIENT EMAIL COPY TO ADMIN
    $mail->AddBCC("oded@orderapp.com");
    //Address to which recipient will reply
    $mail->addReplyTo("orders@orderapp.com", "Reply");


    //Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = 'הזמנה חדשה '." ".$user_order['restaurantTitleHe']." ".'עסק';
    $mail->Body = $mailbody;
    $mail->AltBody = "OrderApp";

    if (!$mail->send())
    {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
    else
    {
        echo "Message has been sent successfully";
    }

}

function email_order_summary_hebrew_admin($user_order,$orderId,$todayDate)
{

    $mailbody  = '<html><head><meta charset="UTF-8"></head>';
    $mailbody  .= '<body style="padding: 0; margin: 0" >';
    $mailbody  .= '<div style="max-width: 600px; margin: 0 auto; border: 1px solid #D3D3D3; border-radius: 5px; overflow: hidden ">';
    $mailbody  .= '<style>';
    $mailbody  .= '@import url("https://fonts.googleapis.com/css?family=Open+Sans:300");';
    $mailbody  .= '</style>';
    $mailbody  .= '<div style="font-family: Open Sans">';
    $mailbody  .= '<div style="background-image: url(http://dev.orderapp.com/restapi/images/header.png); background-repeat: no-repeat; background-position: center; background-size: cover;" >';
    $mailbody  .= '<table style="width: 100%; color: white; padding: 30px">';
    $mailbody  .= '<tr style="font-size: 30px; padding: 10px">';
    $mailbody  .= '<td dir="rtl" style="text-align: left">'.$user_order['total_paid'].' ש"ח'.'</td>';
    $mailbody  .= '<td style="text-align: right;" >  סיכום הזמנה <img style="padding-top: 10px; width: 20px" src="http://dev.orderapp.com/restapi/images/bag.png" ></td>';
    $mailbody  .= '</tr>';
    $mailbody  .= '<tr style="font-size: 12px; padding: 10px" >';
    $mailbody  .= '<td>'.$user_order['payment_option'].'</td>';
    $mailbody  .= '<td style="text-align: right" dir="rtl">';
    $mailbody  .=  '&nbsp;'.$todayDate.'&nbsp;';
    $mailbody  .= 'מספר הזמנה #';
    $mailbody  .=  $orderId;
    $mailbody  .= '</tr>';
    $mailbody  .= '</table>';
    $mailbody  .= '</div>';
    $mailbody  .= '<div  style="padding: 10px 30px 0px 30px;" >';


    foreach($user_order['rests_orders'][0]['foodCartData']  as $t) {

        $mailbody.='<table style="width: 100%; color:black; padding: 30px 0; border-bottom: 1px solid #D3D3D3" >';
        $mailbody.='<tr style="font-size: 18px; padding: 10px; font-weight: bold" >';
        $mailbody.='<span style="color: #FF864C;" dir="rtl">';
        $mailbody.=(($t['price'] * $t['qty'])).'ש"ח';
        $mailbody.='</span> &nbsp; <span dir="rtl">ש"ח</span>';
        $mailbody.=$t['price'].' x '.$t['qty'].'</td>';
        $mailbody.='<td style="text-align: right;" >'. $t['name_he'] .'</td>';
        $mailbody.='</tr>';
        $mailbody.='<tr style="font-size: 12px; padding: 5px 10px; color: #808080" >';
        $mailbody.='<td > </td>';
        if($t['specialRequest'] != "") {

            if ($t['detail_he'] == '') {


                $mailbody.='<td style="text-align: right; padding: 5px" dir="rtl">'.$t['detail_he'].' הערות : '.$t['specialRequest'].'</td>';

            }
            else {

                $mailbody.='<td style="text-align: right; padding: 5px" dir="rtl">'.$t['detail_he'].', הערות : '.$t['specialRequest'].'</td>';

            }
        }
        else
        {
            $mailbody.='<td style="text-align: right; padding: 5px" dir="rtl">'.$t['detail_he'].'</td>';

        }
        $mailbody.='</tr>';
        $mailbody.='</table>';
    }


    $mailbody .=  '</div>';
    if($user_order['specialRequest'] != '')
    {

        $mailbody .= '<br><span style="color: #000000;text-align: right;float: right;" dir="rtl"> <span style="color: #808080; padding:10px 30px;">בקשה מיוחדת :</span>'.$user_order["specialRequest"].'</span><br>';

    }

    $mailbody .= '<table style="width: 100%; color:black; padding:10px 30px; background: #FEF2E8; border-bottom: 1px solid #D3D3D3 ">';

    if($user_order['isCoupon'] == "false")
    {

        $mailbody .= '<tr style="font-size: 18px;  font-weight: bold">';
        $mailbody .= '<td style=" white-space: nowrap"> <span style="color: #FF864C;" dir="rtl">'.$user_order['total_paid'].' ש"ח '.'</span></td>';
        $mailbody .= '<td style="padding: 5px 0; text-align: right; " > סה"כ </td>';
        $mailbody .= '</tr>';

    }
    else
    {
        $mailbody .= '<tr style="font-size: 18px;  font-weight: bold">';
        $mailbody .= '<td style=" white-space: nowrap"> <span style="color: #FF864C;" >'.$user_order['actual_total'].' ש"ח '.'</span></td>';
        $mailbody .= '<td style="padding: 5px 0; text-align: right; " > סיכום ביניים </td>';
        $mailbody .= '</tr>';

    }

    //TODAY REMAINING BALANCE SECTION
    $mailbody .= '<tr style="font-size: 18px;  font-weight: bold" >';
    $mailbody .= '<td style="white-space: nowrap"> <span style="color: #FF864C;" > '.$user_order['discount'].' ש"ח  '.'</span></td>';
    $mailbody .= '<td style="padding: 5px 0; text-align: right;" >יתרת היום* </td>';
    $mailbody .= '</tr>';

    $mailbody .= '</table>';

    $mailbody .= '<table style="float: right;color:black; padding:10px 30px; width: 100%; position: relative; left: calc(100% - 270px)" cellspacing="5px">';
    $mailbody .= '<tr style="font-size: 18px;  font-weight: bold" >';
    $mailbody .= '<td colspan="2" style="padding: 10px 0; text-align: right" dir="rtl" > מידע ללקוחות   </td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';
    $mailbody .= '<td style="text-align: right; white-space: nowrap"> '.$user_order['user']['name'].' </td>';
    $mailbody .= '<td style="padding: 10px 0"><img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_user.png"></td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';
    $mailbody .= '<td style="text-align: right; white-space: nowrap"> '.$user_order['user']['contact'].' </td>';
    $mailbody .= '<td style="padding: 10px 0"><img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_phone.png"></td>';
    $mailbody .= '</tr>';

    //COMPANY INFO
    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';
    $mailbody .= '<td style="text-align: right; white-space: nowrap"> '.$user_order['company']['company_name'].' </td>';
    $mailbody .= '<td style="padding: 10px 0"><img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_company.png"></td>';
    $mailbody .= '</tr>';
    //COMPANY INFO ENDS



    $mailbody .= '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';

    //COMPANY ADDRESS
    $mailbody .= '<td style="text-align: right; white-space: nowrap" dir="rtl"> לספק ב : '.$user_order['company']['company_address'].'</td>';


    $mailbody .=  '<td style="padding: 10px 0; text-align: center"> <img style="height: 24px" src="http://dev.orderapp.com/restapi/images/ic_location.png" ></td>';
    $mailbody .=  '</tr>';
    $mailbody .=  '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';
    $mailbody .=  '<td style="text-align: right; white-space: nowrap">'.$user_order['user']['email'].'</td>';
    $mailbody .=  '<td style="padding: 10px 0;"><img style="width: 20px" src="http://dev.orderapp.com/restapi/images/ic_email.png" ></td>';
    $mailbody .=  '</tr>';
    $mailbody .=  '<tr style="font-size: 12px; padding: 5px 10px; color: #808080">';
    $mailbody .=  '<td style="text-align: right; white-space: nowrap">'.$user_order['payment_option'].'</td>';
    $mailbody .=  '<td style="padding: 10px 0;" > <img style=" width: 20px" src="http://dev.orderapp.com/restapi/images/ic_card.png" ></td>';
    $mailbody .=  '</tr>';
    $mailbody .=  '</table>';
    $mailbody .=  '</div></div></body></html>';



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
    $mail->addAddress(EMAIL);                    //SEND ADMIN EMAIL


    //Address to which recipient will reply
    $mail->addReplyTo("orders@orderapp.com", "Reply");


    //Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = 'עסק'." ".['rests_orders'][0]['selectedRestaurant']['name_he']." הזמנה חדשה # "."  ".$orderId;
    $mail->Body = $mailbody;
    $mail->AltBody = "OrderApp";

    if (!$mail->send())
    {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
    else
    {
        echo "Message has been sent successfully";
    }

}




function email_for_mark2($user_order,$orderId,$todayDate)
{

    $mailbody = '';

    // USER NAME

    $mailbody .= 'Name :'. $user_order['name'];
    $mailbody .= '\n';

    // USER EMAIL

    $mailbody .= 'Email :'. $user_order['email'];
    $mailbody .= '\n';

    // USER CONTACT

    $mailbody .= 'Contact :'. $user_order['contact'];
    $mailbody .= '\n';

    // COMPANY NAME

    $mailbody .= ' Company Name' . $user_order['company_name'];
    $mailbody .= '\n';


    // RESTAURANT NAME
    $mailbody .= 'Restaurant Name :'. $user_order['restaurantTitle'];
    $mailbody .= '\n';


    //  PAYMENT METHOD CASH OR CREDIT CARD

    $mailbody .= 'Payment Method : '.$user_order['Cash_Card'];
    $mailbody .= '\n';




    $mailbody .= 'Delivery at Company Address : '. $user_order['deliveryAddress'];
    $mailbody .= '\n';



    if($user_order['isCoupon']) {

        $mailbody .= '\n';
        $mailbody .= 'coupon code : ' . $user_order['couponCode'];
        $mailbody .= '\n';


        if ($user_order['isFixAmountCoupon'] == 'true') {


            $mailbody .= 'Discount : ' . $user_order['discount_coupon'] . ' NIS';

        }
        else {

            $mailbody .= 'Discount : ' . $user_order['discount_coupon'] . ' %';

        }

        $mailbody .= '\n';
    }



    foreach($user_order['cartData'] as $t) {


        if($t['specialRequest'] != "") {


            if ($t['detail'] != '') {

                $mailbody .= 'Special Request : '.$t['specialRequest'];

            }
            else {

                $mailbody .= 'Special Request : '.$t['specialRequest'];
            }

            $mailbody .= '\n';
        }

    }


    $mailbody .= 'Special Request : '.$user_order['specialRequest'];
    $mailbody .= '\n';


    $mailbody .= 'Sub Total : '.$user_order['totalWithoutDiscount'];
    $mailbody .= '\n';


    $mailbody .= 'Total : '.$user_order['total'];
    $mailbody .= '\n';


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
    $mail->addAddress(EMAIL);                    //SEND ADMIN EMAIL


    //Address to which recipient will reply
    $mail->addReplyTo("orders@orderapp.com", "Reply");


    //Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = 'Ledger '.$user_order['restaurantTitle'].' Order# '.$orderId;
    $mail->Body = $mailbody;
    $mail->AltBody = "OrderApp";


    if (!$mail->send()) {

        echo "Mailer Error: " . $mail->ErrorInfo;
    }
    else {

        echo "Message has been sent successfully";

    }

}


// ADMIN EMAIL
// EMAIL ORDER SUMMARY HEBREW VERSION FOR ADMIN
function email_for_kitchen($user_order,$orderId,$todayDate)
{
    $mailbody = '';

    // USER NAME
    $mailbody .=  $user_order['user']['name'].' : שֵׁם';
    $mailbody .= '<br>';
    $mailbody .= '<br>';


    $mailbody .=  $user_order['company']['company_name'].' : שם החברה';
    $mailbody .= '<br>';
    $mailbody .= '<br>';


    $mailbody .= ' :  הזמנה';

    foreach($user_order['rests_orders'][0]['foodCartData']  as $t)
    {

        $mailbody .= '<br>';
        $mailbody .= '<br>';


        $mailbody.= $t['name_he'];

        $mailbody .= '<br>';
        $mailbody .= '<br>';

        $mailbody .=  $t['detail_he'];

        $mailbody .= '<br>';
        $mailbody .= '<br>';

        if($t['specialRequest'] != "") {


            if ($t['detail_he'] != '')
            {

                $mailbody .= $t['specialRequest'].' : בקשה מיוחדת';

            }
            else {

                $mailbody .= $t['specialRequest'].' : בקשה מיוחדת';
            }


        }

        $mailbody .= '<br>';
        $mailbody .= '<br>';
        $mailbody .= '<br>';
        $mailbody .= '<br>';

    }


    $mailbody .= $user_order['actual_total'].' : סך כל החשבון ללא דיסקונט';
    $mailbody .= '<br>';
    $mailbody .= '<br>';


    $mailbody .= $user_order['total_paid'].' : סה"כ';
    $mailbody .= '<br>';
    $mailbody .= '<br>';



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
    $mail->addAddress(EMAIL);                    //SEND ADMIN EMAIL


    //Address to which recipient will reply
    $mail->addReplyTo("orders@orderapp.com", "Reply");


    //Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = " הזמנה חדשה ".substr($user_order['user']['contact'], -4) . " #" . $user_order['rests_orders'][0]['selectedRestaurant']['name_he'];
    $mail->Body = $mailbody;
    $mail->AltBody = "OrderApp";

    if (!$mail->send()) {

        echo "Mailer Error: " . $mail->ErrorInfo;

    }
    else {

        echo "Message has been sent successfully";

    }

}


