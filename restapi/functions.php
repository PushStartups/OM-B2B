<?php

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