<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once(dirname(__FILE__) . '/../../inc/initDb.php');
require_once (dirname(__FILE__) . '/../../PHPMailer/PHPMailerAutoload.php');
require_once (dirname(__FILE__) . '/../../Interfax/vendor/autoload.php');

use Mailgun\Mailgun;
use Interfax\Client;

DB::query("set names utf8");

//GET ALL ORDERS FOR TODAY SORTED BY RESTAURANT
$restaurants_list = getOrders();
//POPULATE RESTAURANTS WITH DATA (EMAIL, FAX, WHATSAPP)
$ready_restaurants_list = populateWithData($restaurants_list);
//
sendMessages($ready_restaurants_list);

echo '<pre>'; var_dump($ready_restaurants_list); echo '</pre>';

function sendMessages($ready_restaurants_list) {
  
  foreach($ready_restaurants_list as $restaurant) {
    
    $msg = createMsg($restaurant);
    
    telegramAPI($msg, true);
  
  }
}

function createMsg($restaurant) {
  
  $total_sum = 0;
  
  $msg = $restaurant["name_he"] . "
";

  $msg .= $restaurant["name_he"] . " OrderApp " . "
";
  $msg .= $restaurant["whatsapp_group_creator"] . "

";
  
  $msg .= "רשימת הזמנות היום" . "

";
  
  foreach($restaurant["orders"] as $order) {
    $total_sum = $total_sum + $order["sum_order"];
    
    $msg .=  "תאריך " . "*" . $order["date"] . "
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
  $restaurants_data_array = DB::query("SELECT `id`, `fax_number`, `email`, `name_en`, `name_he`, `whatsapp_group_name`, `whatsapp_group_creator`, `balance` FROM `restaurants` WHERE id IN (". implode(",", $restaurants_ids_array) . ")");
  
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
//  $left_restaurants_data = DB::query("SELECT `id`, `fax_number`, `email`, `name_en`, `name_he`, `whatsapp_group_name`, `whatsapp_group_creator`, `balance` FROM `restaurants` WHERE id IN (" . implode(",", $left_restaurants_ids) . ")");
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