<?php
require "inc/initDb.php";
require '../restapi/PHPMailer/PHPMailerAutoload.php';
if(isset($_POST["Import"])){


    $last_id = DB::queryFirstRow("SELECT max(id) FROM b2b_users");
    $last_user_id = $last_id['id'];
    $company_id = $_POST['company_id'];


    $company = DB::queryFirstRow("SELECT * FROM company where id = '$company_id'");
    $discount = $company['discount'];


    $filename = $_FILES["file"]["tmp_name"];


    if($_FILES["file"]["size"] > 0)
    {
        $file = fopen($filename, "r");
        $counter = 0;
        while (($getData = fgetcsv($file, 10000, ",")) !== FALSE)
        {

            if($counter != 0)
            {
                $last_user_id++;
                $password = $getData[1].rand(100,9999);
                DB::insert('b2b_users', array(
                    "smooch_id"         => $getData[0],
                    "name"              => $getData[1],
                    "user_name"         => strtolower($getData[1]),
                    "password"          => $password,
                    "discount"          => $discount,
                    "date"              => DB::sqleval("NOW()"),
                    "contact"           => $getData[2],
                    "address"           => $getData[3],
                    "state"             => 0,
                    "language"          => "english",
                    "payment_url"       => NULL,
                    "extras"            => NULL,
                    "restaurant_id"     => NULL,
                    "role_id"           => NULL,
                    "company_id"        => $company_id,
                    "voucherify_id"     => 0
                ));

                $service_url = $_SERVER['HTTP_HOST'].'/restapi/index.php/send_email_to_b2b_users';
                $curl = curl_init($service_url);
                $curl_post_data = array(
                    "email"     => $getData[0],
                    "password"  => $password,
                    "user_name" => strtolower($getData[1])
                );
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
                $curl_response = curl_exec($curl);
                curl_close($curl);



               // email_to_b2b_users($getData[0],$password,strtolower($getData[1]));
               // ob_end_clean();
            }
            $counter++;

        }

        fclose($file);
        header("location:companies.php");
    }
}
?>


<?php
function email_to_b2b_users($email,$password,$username)
{

    $mailbody = '<html lang="en">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>OrderApp</title>';

    //    <style type="text/css">
    //    </style>
    $mailbody .= '</head><body bgcolor="#f7f7f7" style="background: #f7f7f7;">';
    $mailbody .= '<table border="0" cellpadding="0" cellspacing="0" width="600" id="templateColumns" style="color: #000; font-size: 16px; line-height: 18px; font-weight: 400; width: 600px; margin: 0 auto; font-family: Arial, Helvetica, sans-serif;">';
    $mailbody .= '<tr><td align="left" valign="top" width="100%" bgcolor="#ff7f00" style="background: #ff7f00; font-size: 18px; line-height: 22px; font-weight: 700;padding: 20px;">';
    $mailbody .= '<img style="display: inline-block; vertical-align: middle; margin: 0;" src="https://dev.orderapp.com/admin/img/email-logo.png" alt="orderapp">';
    $mailbody .= '<h1 style="display: inline-block; vertical-align: middle; margin: 0 10px; font-weight: 400; color: #fff;">Welcome to OrderApp!</h1></td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr>';
    $mailbody .= '<td align="left" valign="top" width="100%" bgcolor="#FFFFFF" style="padding: 50px 25px; background: #fff;">';
    $mailbody .= '<p>Hi '.$username.' <br>Your login details are as follows:</p>';
    $mailbody .= '<p><b>Username : </b> '.$username.'</p>';
    $mailbody .= '<p><b>Password : </b> '.$password.'</p><br>';
    $mailbody .= '<p>Visit Website :<a style="color: #3b5998; text-decoration: none;" href="#">https://devb2b.orderapp.com/en/</a></p>';
    $mailbody .= '</td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr>';
    $mailbody .= '<td align="center" style="padding: 100px 0 20px;">';
    $mailbody .= '<table border="0" cellspacing="0" cellpadding="0">';
    $mailbody .= '<tr><td width="37" style="text-align: center; padding: 0 8px;"><a href="#"><img src="https://dev.orderapp.com/admin/img/fb.png" width="37" height="37" alt="Facebook" border="0" /></a></td>';
    $mailbody .= '<td width="37" style="text-align: center; padding: 0 8px;"><a href="#"><img src="https://dev.orderapp.com/admin/img/tw.png" width="37" height="37" alt="Twitter" border="0" /></a></td>';
    $mailbody .= '<td width="37" style="text-align: center; padding: 0 8px;"><a href="#"> <img src="https://dev.orderapp.com/admin/img/gp.png" width="37" height="37" alt="Facebook" border="0" /></a></td>';
    $mailbody .= '<td width="37" style="text-align: center; padding: 0 8px;"><a href="#"><img src="https://dev.orderapp.com/admin/img/insta.png" width="37" height="37" alt="Twitter" border="0" /></a></td>';
    $mailbody .= '<td width="37" style="text-align: center; padding: 0 8px;"><a href="#"><img src="https://dev.orderapp.com/admin/img/pin.png" width="37" height="37" alt="Facebook" border="0" /></a></td>';
    $mailbody .= '<td width="37" style="text-align: center; padding: 0 8px;"><a href="#"><img src="https://dev.orderapp.com/admin/img/link.png" width="37" height="37" alt="Twitter" border="0" /></a></td>';
    $mailbody .= '</tr>';
    $mailbody .= '</table>';
    $mailbody .= '</td>';
    $mailbody .= '</tr>';
    $mailbody .= '<tr>';
    $mailbody .= '<td><p style="margin: 0; text-align: center;"><a style="color: #797979; text-decoration: none;" href="https://dev.orderapp.com/en/">orderapp.com</a></p></td></tr></table></body></html>';

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
    $mail->From = "order@orderapp.com";
    $mail->FromName = "OrderApp";


    //To address and name
    $mail->addAddress($email);                    //SEND ADMIN EMAIL


    //Address to which recipient will reply
    $mail->addReplyTo("order@orderapp.com", "Reply");


    //Send HTML or Plain Text email
    $mail->isHTML(false);
    $mail->Subject = "B2B OrderApp Credentials Info";
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
?>