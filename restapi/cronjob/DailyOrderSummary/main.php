<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once(dirname(__FILE__) . '/../../vendor/autoload.php');
require_once(dirname(__FILE__) . '/../../inc/initDb.php');
require_once (dirname(__FILE__) . '/../../PHPMailer/PHPMailerAutoload.php');
require_once (dirname(__FILE__) . '/../../Interfax/vendor/autoload.php');

use Mailgun\Mailgun;
use Interfax\Client;
//use sngrl\PhpFirebaseCloudMessaging\FirebaseClient;
//use sngrl\PhpFirebaseCloudMessaging\Message;
//use sngrl\PhpFirebaseCloudMessaging\Recipient\Device;
//use sngrl\PhpFirebaseCloudMessaging\Notification;

const DEFAULT_URL = 'https://hunters-chat.firebaseio.com/';
const DEFAULT_TOKEN = 'wSZSOmMJuloBAS5xa82Q7S7wzJ2ErDuwxadJiIJD';
const DEFAULT_PATH = 'user-messages/';

//const DEFAULT_URL = 'https://test-6a55f.firebaseio.com';
//const DEFAULT_TOKEN = 'AIzaSyCEF7WtK0fu8LtD2dahcOhUX27HaUqyFH4';

//$firebase = new \Firebase\FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);

DB::query("set names utf8");

const TEST_MODE = true;

main();



function main() {
  //GET ALL ORDERS FOR TODAY SORTED BY RESTAURANT
  $restaurants_list = getOrders();
//POPULATE RESTAURANTS WITH DATA (EMAIL, FAX, WHATSAPP)
  if ( count($restaurants_list) > 0 ) {
    $ready_restaurants_list = populateWithData($restaurants_list);
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
      sendOrderEmail($msg, $restaurant["email"]);
    }
    
    if($restaurant["firebase_chat_id"] && !is_null($restaurant["firebase_chat_id"]) ) {
      firebaseMsg($restaurant["firebase_chat_id"], $msg, $TEST_MODE);
    }
    
    
    
  }
}

function createMsg($restaurant) {
  
  $total_sum = 0;
  
  $msg = $restaurant["name_he"] . "
";
  $msg .= $restaurant["whatsapp_group_name"] . "
";
//  $msg .= $restaurant["name_he"] . " OrderApp " . "";
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

function populateWithData($restaurants_list) {
  $restaurants_ids_array = array();
  foreach( $restaurants_list as $restaurant ) {
    array_push($restaurants_ids_array, $restaurant["id"]);
  }
  
  DB::useDB('orderapp_restaurants_b2b_wui');
  $restaurants_data_array = DB::query("SELECT `id`, `fax_number`, `email`, `name_en`, `name_he`, `whatsapp_group_name`, `whatsapp_group_creator`, `balance`, `firebase_chat_id` FROM `restaurants` WHERE id IN (". implode(",", $restaurants_ids_array) . ")");
  
  foreach($restaurants_list as &$restaurant) {
    foreach($restaurants_data_array as $data_item) {
      if ( $restaurant["id"] == $data_item["id"] ) {
        $restaurant["email"] = $data_item["email"];
        $restaurant["fax_number"] = $data_item["fax_number"];
        $restaurant["name_en"] = $data_item["name_en"];
        $restaurant["name_he"] = $data_item["name_he"];
        $restaurant["whatsapp_group_name"] = $data_item["whatsapp_group_name"];
        $restaurant["whatsapp_group_creator"] = $data_item["whatsapp_group_creator"];
        $restaurant["balance"] = $data_item["balance"];
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
//      "fax" => '',
//      "email" => '',
//      "whatsapp_data" => '',
//      "balance" => '',
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

//function firebaseMessage() {
//  $server_key = 'wSZSOmMJuloBAS5xa82Q7S7wzJ2ErDuwxadJiIJD'; //_YOUR_SERVER_KEY_
//  $client = new Client();
//  $client->setApiKey($server_key);
//  $client->injectGuzzleHttpClient(new \GuzzleHttp\Client());
//
//  $message = new Message();
//  $message->setPriority('high');
//  $message->addRecipient(new Device('-Ksm_bXAUCt1exAbXHWu')); //_YOUR_DEVICE_TOKEN_
//  $message
//    ->setNotification(new Notification('some test title', 'some test body'))
//    ->setData(['key' => 'value'])
//  ;
//
//  $response = $client->send($message);
//  var_dump($response->getStatusCode());
//  var_dump($response->getBody()->getContents());
//}

function firebaseMsg($toId, $msg, $test_mode) {

  $fromId = 'xgUkB30C4BZwqwEqRiAait7Jsv83';
  if ($test_mode) {
    $toId = $toId = '-Ksm_bXAUCt1exAbXHWu';
  }
  
  
  $response = firebaseSendMsg($toId, $fromId, $msg);
  $responseArr = explode(":", $response);
  $message_name = preg_replace('/}/', "", preg_replace('/"/', "", $responseArr[1]));
  
  echo $message_name;
  echo "<br>";
  
  $f = updateFirebaseUserMsg($fromId, $toId, $message_name);
  $s = updateFirebaseUserMsg($toId, $fromId, $message_name);
  
//  echo "<br>";
//  var_dump($f);
//  echo "<br>";
//  var_dump($s);
}

function firebaseSendMsg($toId, $fromId, $msg) {
  
  $data = array(
    "toId" => $toId,
    "text"  => $msg,
    "fromId"  => $fromId,
    "isToGroup"  => true,
    "isDeleted"  => false,
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