<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once(dirname(__FILE__) . '/../../vendor/autoload.php');
require_once(dirname(__FILE__) . '/../../inc/initDb.php');
require_once (dirname(__FILE__) . '/../../Interfax/vendor/autoload.php');

use Mailgun\Mailgun;
use Interfax\Client;


DB::query("set names utf8");

define('TEST_MODE', false);

//RUN MAIN FUNCTION TO GET ORDERS AND SEND MESSAGES
main();


function main() {
  //GET ALL ORDERS FOR TODAY SORTED BY RESTAURANT
  $restaurants_list = getOrders();
  
  
  if ( count($restaurants_list) > 0 ) {
  
    //POPULATE RESTAURANTS WITH DATA (EMAIL, FAX, WHATSAPP)
    $ready_restaurants_list = populateWithData($restaurants_list);
    
    //SEND MESSAGES TO EVERY RESTAURANT FROM THE ARRAY $ready_restaurants_list
    sendMessages($ready_restaurants_list, TEST_MODE);
    
    echo '<pre>'; var_dump($ready_restaurants_list); echo '</pre>';
  } else {
    echo "No orders for the last 24h.";
  }
}

function sendMessages($ready_restaurants_list, $TEST_MODE) {
  
  foreach($ready_restaurants_list as $restaurant) {
    
    $msg = createMsg($restaurant);
    
    telegramAPI($msg, $TEST_MODE);
    whatsappAPI($restaurant["whatsapp_group_creator"], $restaurant["whatsapp_group_name"], $msg, $TEST_MODE);
    
    if ( $restaurant["fax_number"] ) {
      sendFax($restaurant["fax_number"], $msg, $TEST_MODE);
    }
    
    if ( $restaurant["email"] ) {
      sendEmail(createEmailMsg($restaurant), $restaurant["email"]);
    }
    
    //SENDS FIREBASE MESSAGE
    if( ($restaurant["firebase_chat_id"] && $restaurant["firebase_chat_id"] !== "NULL") || $TEST_MODE ) {
      firebaseMsg($restaurant["firebase_chat_id"], $msg, $TEST_MODE);
    }
  
  }
}


//CREATE MESSAGE FOR TELEGRAM, WHATSAPP AND FAX
function createMsg($restaurant) {
  
  $total_sum = 0;
  
  $msg = $restaurant["name_he"] . "
";
  $msg .= $restaurant["whatsapp_group_name"] . "
";
  $msg .= $restaurant["whatsapp_group_creator"] . "

";
  
  $msg .= "רשימת הזמנות היום" . "

";
  
  foreach($restaurant["orders"] as $order) {
    $total_sum = $total_sum + $order["sum_order"];
    
    $dateArr = explode('-', $order["date"]);
    $day = $dateArr[0];
    $month = $dateArr[1];
    $year = $dateArr[2];
    $msg .=  "תאריך " . $year . "-" . $month . "-" . $day . "*
";
    $msg .= "זמן " . $order["time"] . "
";
    $msg .= "מספר הזמנה " . $order["id"] . "
";
    $msg .= "סכום ההזמנה " . $order["sum_order"] . "

";
  }
  $msg .= "
";
  $msg .= "-----------------" . "
";

  $msg .= "סה״כ הזמנות " . count($restaurant["orders"]) . "
";
  $msg .=  "סכום סה״כ הזמנות " .$total_sum . "

";
  
  $msg .= "יתרה " . $restaurant["balance"];


  return $msg;
}

function createEmailMsg($restaurant) {
  
  $total_sum = 0;
  $msg = "<html>";
  $msg .= "<body dir='rtl'>";
  
  $msg .= "<p>" . $restaurant["name_he"] . "</p>";
  $msg .= "<p>" . $restaurant["whatsapp_group_name"] . "</p>";
  $msg .= "<p>" . $restaurant["whatsapp_group_creator"] . "</p>";
  $msg .= "<br>";
  
  $msg .= "<p>" . "רשימת הזמנות היום" . "</p>";
  $msg .= "<br>";
  
  foreach($restaurant["orders"] as $order) {
    $total_sum = $total_sum + $order["sum_order"];
    
    $dateArr = explode('-', $order["date"]);
    $day = $dateArr[0];
    $month = $dateArr[1];
    $year = $dateArr[2];
    $msg .= "<p>" . "תאריך " . $year . "-" . $month . "-" . $day . "*" . "</p>";
    $msg .= "<p>" . "זמן " . $order["time"] . "</p>";
    $msg .= "<p>" . "מספר הזמנה " . $order["id"] . "</p>";
    $msg .= "<p>". "סכום ההזמנה " . $order["sum_order"] . "</p>";
    $msg .= "<br>";
  }
  
  $msg .= "<p>" . "-----------------" . "</p>";
  
  $msg .= "<p>" . "סה״כ הזמנות " . count($restaurant["orders"]) . "</p>";
  $msg .=  "<p>" . "סכום סה״כ הזמנות " .$total_sum . "</p>";
  $msg .= "<br>";
  
  $msg .= "<p>" . "יתרה " . $restaurant["balance"] . "</p>";
  $msg .= "</body>";
  $msg .= "</html>";
  
  
  return $msg;
}

function populateWithData($restaurants_list) {
  $restaurants_ids_array = array();
  foreach( $restaurants_list as $restaurant ) {
    array_push($restaurants_ids_array, $restaurant["id"]);
  }
  
  DB::useDB('orderapp_restaurants_b2b_wui');
  $restaurants_data_array = DB::query("SELECT `id`, `fax_number`, `email`, `name_en`, `name_he`, `whatsapp_group_name`, `whatsapp_group_creator`, `firebase_chat_id` FROM `restaurants` WHERE id IN (". implode(",", $restaurants_ids_array) . ")");
  
  $restaurants_balances = getBalancesForRestaurants($restaurants_ids_array);
  
  foreach($restaurants_list as &$restaurant) {
    foreach($restaurants_data_array as $data_item) {
      if ( $restaurant["id"] == $data_item["id"] ) {
        $restaurant["email"] = $data_item["email"];
        $restaurant["fax_number"] = $data_item["fax_number"];
        $restaurant["name_en"] = $data_item["name_en"];
        $restaurant["name_he"] = $data_item["name_he"];
        $restaurant["whatsapp_group_name"] = $data_item["whatsapp_group_name"];
        $restaurant["whatsapp_group_creator"] = $data_item["whatsapp_group_creator"];
        $restaurant["balance"] = $restaurants_balances[$restaurant["id"]];
        $restaurant["firebase_chat_id"] = $data_item["firebase_chat_id"];
        $restaurant["ready"] = true;
      }
    }
  }
  
  
  //IF THERE WERE NO NEEDED RESTAURANT IN 'orderapp_restaurants_b2b_wui' LOOK IN 'orderapp_restaurants'
//  $left_restaurants_ids = array();
//  foreach($restaurants_list as $restaurant) {
//    if ( !$restaurant["ready"] ) {
//      array_push($left_restaurants, $restaurant["id"]);
//    }
//  }
//
//  DB::useDB('orderapp_restaurants');
//  $left_restaurants_data = DB::query("SELECT `id`, `fax_number`, `email`, `name_en`, `name_he`, `whatsapp_group_name`, `whatsapp_group_creator`, `balance`, `firebase_chat_id` FROM `restaurants` WHERE id IN (" . implode(",", $left_restaurants_ids) . ")");
//
//  foreach($restaurants_list as &$restaurant) {
//    foreach($left_restaurants_data as $data_item) {
//      if ( $restaurant["id"] == $data_item["id"] ) {
//        $restaurant["email"] = $data_item["email"];
//        $restaurant["fax_number"] = $data_item["fax_number"];
//        $restaurant["name_en"] = $data_item["name_en"];
//        $restaurant["name_he"] = $data_item["name_he"];
//        $restaurant["whatsapp_group_name"] = $data_item["whatsapp_group_name"];
//        $restaurant["whatsapp_group_creator"] = $data_item["whatsapp_group_creator"];
//        $restaurant["balance"] = $data_item["balance"];
//        $restaurant["firebase_chat_id"] = $data_item["firebase_chat_id"];
//        $restaurant["ready"] = true;
//      }
//    }
//  }
  
  
  return $restaurants_list;
}

//GET BALANCES FOR RESTAURANTS
function getBalancesForRestaurants($restaurants_ids_array) {
  $balances_for_restaurants = array();
  
  DB::useDB(B2B_B2C_COMMON);
  $balances = DB::query("SELECT id, balance from restaurant_balance WHERE id IN(" . implode(",", $restaurants_ids_array) . ")");
//  echo '<pre>'; var_dump($balances); echo '</pre>';
  
  foreach($restaurants_ids_array as $rest_id) {
    foreach($balances as $balance) {
      if($rest_id == $balance["id"]) {
        $balances_for_restaurants[$balance["id"]] = $balance["balance"];
      }
    }
  }
  
  echo '<pre>'; var_dump($balances_for_restaurants); echo '</pre>';
  return $balances_for_restaurants;
}

function getOrders() {
  
  //GET ORDERS FROM B2B
  $b2b_orders = getOrdersFromB2B();
  //GET ORDERS FROM B2C
  $b2c_orders = getOrdersFromB2C();
  //COMBINE ORDERS
  $today_orders = array_merge($b2b_orders, $b2c_orders);
  
  $restaurant_array = array();
  
  foreach($today_orders as $order ) {
    $restaurant = array(
      "id" => '',
      "orders"=> array(),
    );
    if ( count($restaurant_array) == 0 ) {
      $restaurant["id"] = $order["restaurant_id"];
      array_push($restaurant["orders"], $order);
      array_push($restaurant_array, $restaurant);
    } else {
      foreach($restaurant_array as &$array_item) {
        if ( $array_item["id"] == $order["restaurant_id"] ) {
          array_push($array_item["orders"], $order);
          continue 2;
        }
      }
      $restaurant["id"] = $order["restaurant_id"];
      array_push($restaurant["orders"], $order);
      array_push($restaurant_array, $restaurant);
    }
  }
  
  return $restaurant_array;
}

function getOrdersFromB2B() {
  
  DB::useDB('orderapp_b2b_wui');
  $result = DB::query("SELECT * FROM `b2b_orders` WHERE date >= now() - INTERVAL 1 DAY AND `order_status` != 'cancelled'");
  $orders_list = array();
  
  $orders_item = array(
    "id" => '',
    "date" => '',
    "time" => '',
    "restaurant_id" => '',
    "sum_order" => '',
  );
  
  foreach ($result as $order) {
    
    $date_array = explode(" ", $order["date"]);
    
    $rest_order_obj = json_decode($order["rest_order_object"], true);
    //echo '<pre>'; var_dump($order); echo '</pre>';
    
    $orders_item["id"] = $order["id"];
    $orders_item["date"] = $date_array[0];
    $orders_item["time"] = $date_array[1];
    $orders_item["restaurant_id"] = $order["restaurant_id"];
    $orders_item["sum_order"] = $rest_order_obj["actual_total"];
    
    array_push($orders_list, $orders_item);
    
  }
  
  return $orders_list;
}

function getOrdersFromB2C() {
  
  DB::useDB('orderapp_user');
  $result = DB::query("SELECT * FROM `user_orders` WHERE order_date >= now() - INTERVAL 1 DAY");
  
  $orders_list = array();
  $orders_item = array(
    "id" => '',
    "date" => '',
    "time" => '',
    "restaurant_id" => '',
    "sum_order" => '',
  );
  
  foreach ($result as $order) {
    
    $date_array = explode(" ", $order["order_date"]);
    
    $orders_item["id"] = $order["id"];
    $orders_item["date"] = $date_array[0];
    $orders_item["time"] = $date_array[1];
    $orders_item["restaurant_id"] = $order["restaurant_id"];
    $orders_item["sum_order"] = $order["total"];
    
    array_push($orders_list, $orders_item);
    
  }
  
  return $orders_list;
}

function telegramAPI($text, $TEST_MODE) {
  
  if ( $TEST_MODE ) {
    $bot_id = "271480837:AAEI0i1O3ozIRNyWU-7-qC_hGfOBnUxab88";
    $chat_id = "-222443307";
  } else {
    //LIVE TELEGRAM CREDENTIALS
    $bot_id = "234472538:AAEwJUUgl0nasYLc3nQtGx4N4bzcqFT-ONs";
    $chat_id = "-165732759";
  }
  
  $postData = array(
    'chat_id' => $chat_id,
    'text' => $text
  );
  
  $headers = array(
    'Content-Type: application/json'
  );
  
  $url = 'https://api.telegram.org/bot'.$bot_id.'/sendMessage';
  
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
  $response = curl_exec($ch);
  //echo "Response: ".$response;
  curl_close($ch);
}

function sendEmail($msg, $toEmail) {
  
  if($_SERVER['HTTP_HOST'] == 'eluna.orderapp.com')
  {
    //$subject = "(ELUNA) "+$user_order['restaurantTitle'].' Order# '.$orderId;
  }
  
  else
  {
    $subject = 'Daily Order Summary';
  }
  
  
  //AMAZON SERVER ACTIVATED
  if(ACTIVE_SERVER_ID == '1')
  {
    
    $client = SesClient::factory(array(
      'version'=> 'latest',
      'region' => 'eu-west-1',
      'credentials' => array(
        'key'    => ACCESS_KEY_ID,
        'secret' => ACCESS_KEY_SECRET,
      )
    ));
    
    try {
      
      $result = $client->sendEmail([
        'Destination' => [
          'ToAddresses' => [
            $toEmail,
          ],
        ],
        'Message' => [
          'Body' => [
            'Html' => [
              'Charset' => 'UTF-8',
              'Data' => $msg,
            ]
          ],
          'Subject' => [
            'Charset' => 'UTF-8',
            'Data' => $subject,
          ],
        ],
        'Source' => EMAIL,
      
      ]);
      
      $messageId = $result->get('MessageId');
      
      //echo("Email sent! Message ID: $messageId"."\n");
      
    } catch (SesException $error) {
      
      echo("The email was not sent. Error message: ".$error->getAwsErrorMessage()."\n");
    }
    
  }
  //MAIN GUN SERVER ACTIVATED
  else if(ACTIVE_SERVER_ID == '2'){
    
    $mg = Mailgun::create(MAIL_GUN_API_KEY);
    
    $mg->messages()->send(MAIL_GUN_DOMAIN, [
      'from'    =>  "OrderApp <".EMAIL.">",
      'to'      =>  $toEmail,
      'cc'      =>  EMAIL,
      'subject' =>  $subject,
      'html'    => $msg
    ]);
    
  }
}

function sendOrderEmail($msg, $toEmail) {
  
  $mail = new PHPMailer;
  
  $mail->CharSet = 'UTF-8';
  
  $mail->isSMTP();
  $mail->Host = EMAIL_HOST;                 //   Set mailer to use SMTP
  $mail->SMTPAuth = true;                                             //   Enable SMTP authentication
  $mail->Username = EMAIL_SMTP_USERNAME;
  $mail->Password = EMAIL_SMTP_PASSWORD;   //   SMTP password
  $mail->SMTPSecure = 'tls';                                          //   Enable TLS encryption, `ssl` also accepted
  $mail->Port = 587;
  //From email address and name
  $mail->From = "orders@orderapp.com";
  //$mail->From = "orderapp.orders@gmail.com";
  $mail->FromName = "OrderApp";
  
  
  //To address and name
  $mail->addAddress($toEmail);     //SEND EMAIL TO USER
  
  //$mail->AddCC(EMAIL);           //SEND CLIENT EMAIL COPY TO ADMIN
  
  //Send HTML or Plain Text email
  $mail->isHTML(false);
  
  
  
  $mail->Subject = 'Daily Order Summary';
  $mail->Body = $msg;
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

//FAX SENDING FUNCTION
function sendFax($fax, $msg, $TEST_MODE ) {
  
  $fax_number = '+' . $fax;
  
  if ($TEST_MODE) {
    $fax_number = "+97226544308";
  }
  
  $file = 'FaxMessage.txt';
  file_put_contents($file, $msg);
  
  $interfax = new Client(['username' => 'Orderapp', 'password' => 'pushstartups1!']);
  $fax = $interfax->deliver(['faxNumber' => $fax_number, 'file' => 'FaxMessage.txt']);
  
  // get the latest status:
  $status = $fax->refresh()->status; // Pending if < 0. Error if > 0
  
  echo 'Fax status: ' . $status . '<br>';
  
  // Simple polling
  while ($fax->refresh()->status < 0) {
    sleep(5);
  }
}

function whatsappAPI($groupAdmin, $groupName, $message, $TEST_MODE) {
  
  if ( $TEST_MODE ) {
    $groupAdmin = "972525952665";
    $groupName = "Whatsapp Tests New";
  }
  
  echo $groupAdmin . " - " . $groupName . '<br>';
  
  
  $INSTANCE_ID = "3";
  $CLIENT_ID = "orderapp.orders@gmail.com";
  $CLIENT_SECRET = "59f0a2e52b384082a2f53b687836a65f";
  
  $postData = array(
    'group_admin' => $groupAdmin,
    'group_name' => $groupName,
    'message' => $message
  );
  
  $headers = array(
    'Content-Type: application/json',
    'X-WM-CLIENT-ID: '.$CLIENT_ID,
    'X-WM-CLIENT-SECRET: '.$CLIENT_SECRET
  );
  
  $url = 'http://api.whatsmate.net/v2/whatsapp/group/message/' . $INSTANCE_ID;
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
  $response = curl_exec($ch);
  
  echo $response;
  
  curl_close($ch);
}

function firebaseMsg($toId, $msg, $test_mode) {
  
  $fromId = 'vZ0itdFLNxMxhIQhzT2PAA1Lhb33';
  if ($test_mode) {
    $toId = '-KtpHWXIj_VmA3cUg-p0';
  }
  
  $response = firebaseSendMsg($toId, $fromId, $msg);
  $responseArr = explode(":", $response);
  $message_name = preg_replace('/}/', "", preg_replace('/"/', "", $responseArr[1]));
  
  echo $message_name;
  echo "<br>";
  
  $f = updateFirebaseUserMsg($fromId, $toId, $message_name);
  $s = updateFirebaseUserMsg($toId, $fromId, $message_name);
  
}

function firebaseSendMsg($toId, $fromId, $msg) {
  
  $data = array(
    "toId" => $toId,
    "text"  => $msg,
    "fromId"  => $fromId,
    "isToGroup"  => true,
    "isDeleted"  => false,
    "isEdit" => false,
    "timestamp"  => array(
       ".sv" => "timestamp"
     ),
  );
  
  $data_json = json_encode($data);
  
  $ch = curl_init();
  
  curl_setopt($ch, CURLOPT_URL, "https://hunters-chat.firebaseio.com/messages.json?auth=wSZSOmMJuloBAS5xa82Q7S7wzJ2ErDuwxadJiIJD");
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  
  
  $headers = array();
  $headers[] = "Content-Type: application/x-www-form-urlencoded";
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  
  $result = curl_exec($ch);
  
  if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
  }
  curl_close ($ch);
  
  return $result;
}

function updateFirebaseUserMsg($fromId, $toId, $message_name) {
  
  $vars = array();
  $vars[$message_name] = 1;
  $json_vars = json_encode($vars);
  echo $json_vars;
  
  $ch = curl_init();
  
    curl_setopt($ch, CURLOPT_URL, "https://hunters-chat.firebaseio.com/user-messages/" . $fromId ."/" . $toId ."/.json?auth=wSZSOmMJuloBAS5xa82Q7S7wzJ2ErDuwxadJiIJD");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $json_vars);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
  
  
  $headers = array();
  $headers[] = "Content-Type: application/x-www-form-urlencoded";
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  
  $result = curl_exec($ch);
  if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
  }
  curl_close ($ch);
  
  return $result;
}

