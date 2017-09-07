<?php

require_once (dirname(__FILE__) . '/../PHPMailer/PHPMailerAutoload.php');
require_once (dirname(__FILE__) . '/../Interfax/vendor/autoload.php');

use Mailgun\Mailgun;
use Interfax\Client;

//GET ARRAY OF ALL COMPANIES AND THEIR TIMINGS
function getCompanies($TEST_MODE) {
  $timings = DB::query("SELECT company_id, week_en, delivery_timing, food_ready_for_pickup FROM company_timing");
  //echo '<pre>'; var_dump($timings); echo '</pre>';
  if ( $TEST_MODE ) {
    $companies = DB::query("SELECT id, name, delivery_address, contact_number FROM company WHERE id = 2");
    foreach ( $companies as &$company) {
      $timing = getTimingForCompany($timings, 3);
      $company["timing"] = array();
      foreach( $timing as $day ) {
        array_push($company["timing"], $day);
      }
    }
    return $companies;
  } else {
      $companies = DB::query("SELECT id, name, delivery_address, contact_number FROM company");

      $returnCompanies = array();

      //CHECK IF THE CURRENT TIME IS THE SAME AS THE TIME OF DELIVERY FOR EVERY COMPANY
      foreach($companies as $company) {
        foreach ( $timings as $timing) {
          if ( $timing["company_id"] == $company["id"] ) {
            //foreach($timing as $day) {
              $today = date('l');
              if ( $timing["week_en"] == $today && isSendTime($timing["delivery_timing"]) ) {
                array_push($returnCompanies, $company);
                //echo '<pre>'; var_dump($company); echo '</pre>';
                break;
              }
            //}
          }
        }
      }
      
      return $returnCompanies;
  }
}

//COMPARE THE SEND TIME AND CURRENT TIME
function isSendTime($delivery_timing) {
  if ( count($delivery_timing) != 0 && $delivery_timing != 'Closed' && !is_null($delivery_timing) ) {
    $delivery_time_array = explode(":", $delivery_timing);
    $delivery_hour = $delivery_time_array[0];
    $delivery_minutes = $delivery_time_array[1];
    
    $current_hour = date('H');
    $current_minutes = date('i');

    if( $current_hour == $delivery_hour ){
      if( $current_minutes >= $delivery_minutes && $current_minutes - $delivery_minutes < 14 ){
        return true;
      }
    }
    return false;
  } else {
    return false;
  }
  
}

//GET TIMINGS FOR COMPANY BY ID
function getTimingForCompany($timings, $company_id) {
  $timingForCompany = array();
  foreach ( $timings as $timing) {
    if ( $timing["company_id"] == $company_id ) {
      unset($timing["company_id"]);
      array_push($timingForCompany, $timing);
    }
  }
  return $timingForCompany;
}


//SEND ORDERS TO RESTAURANTS FOR EVERY COMPANY
function sendMessages($companies, $TEST_MODE) {
  foreach($companies as $company) {

    $ordersFromDB = DB::query("SELECT * FROM b2b_orders WHERE `company_id` = " . $company['id'] . " AND `sent` = 0");

    if ( count($ordersFromDB) == 0 ) {
      continue;
    }


    $restaurantsArray = getOrdersByRestaurant($ordersFromDB);

    sendOrderMessage($restaurantsArray, $TEST_MODE);
    sendDeliveryMsg($company, $restaurantsArray, $TEST_MODE);

    //"itemName"
    // echo '<pre>'; var_dump($restaurantsArray); echo '</pre>';

  }
}

//RETURN ARRAY OF RESTAURANTS WITH THEIR ID AND ORDERS
// ARRAY => ( ID => %NUMBER%, ORDER => ARRAY() )
function getOrdersByRestaurant($ordersFromDB) {
  $restaurantsArray = array();

  //echo '<pre>'; var_dump($ordersFromDB); echo '</pre>';

  foreach ( $ordersFromDB as $restaurantOrder ) {

      $rest_order_object = json_decode($restaurantOrder['rest_order_object'], true);

      $restaurant = array(
        "id" => $rest_order_object["rests_orders"][0]["selectedRestaurant"]["id"],
        "orders" => array(
          array(
            "order_id" => $restaurantOrder['id'],
            "rest_order_object" => $rest_order_object,
          )
        ),
      );

      if ( count($restaurantsArray) == 0 ) {
        $restaurantsArray[$restaurant["id"]] = $restaurant;
      } else {
        foreach( $restaurantsArray as &$restaurantItem) {
          // echo $restaurantItem["id"] . " == " . $restaurant["id"];
          // echo '<br>';
          if ( $restaurantItem["id"] == $restaurant["id"] ) {
            array_push($restaurantItem["orders"], $restaurant["orders"][0]);
            break;
          }
          else {
            $restaurantsArray[$restaurant["id"]] = $restaurant;
            break;
          }
        }
      }
    }

    //echo '<pre>'; var_dump($restaurantsArray); echo '</pre>';

    //ADD WHATSAPP GROUPS INFO TO THE RESTAURANTS ARRAYS
    $restaurantsIds = array();
    foreach( $restaurantsArray as $restaurant) {
      array_push($restaurantsIds, $restaurant["id"]);
    }

    DB::useDB('orderapp_restaurants_b2b_wui');
    $whatsapp_rest_data = DB::query("SELECT `id`, `delivery_group`, `whatsapp_group_name` , `whatsapp_group_creator`, `fax_number` , `email` FROM `restaurants` WHERE  `id` IN (". implode(",", $restaurantsIds) . ")");
    //echo '<pre>'; var_dump($whatsapp_rest_data); echo '</pre>';

    foreach($restaurantsArray as &$restaurant) {
      foreach( $whatsapp_rest_data as $item ) {
        if ( $restaurant["id"] == $item["id"] ) {
          $restaurant["delivery_group"] = $item["delivery_group"];
          $restaurant["whatsapp_group_name"] = $item["whatsapp_group_name"];
          $restaurant["whatsapp_group_creator"] = $item["whatsapp_group_creator"];
          $restaurant["fax_number"] = $item["fax_number"];
          $restaurant["email"] = $item["email"];
        }
      }
    }

    return $restaurantsArray;
}

//ASSEMBLE AND SEND MESSAGE FOR RESTAURANT
function sendOrderMessage($restaurantsArray, $TEST_MODE) {

  foreach( $restaurantsArray as $restaurant ) {

      $msg = '';
      $msg .= $restaurant["orders"][0]["rest_order_object"]["rests_orders"][0]["selectedRestaurant"]["name_he"] . "

";
      $msg .= 'הזמנה ללקוח עסקי' . '
';
      $msg .= $restaurant["orders"][0]["rest_order_object"]["company"]["company_name"]. '
';
      $msg .= "------------------------

";
      foreach( $restaurant['orders'] as $order ) {

        $msg .= "שֵׁם : " . $order["rest_order_object"]["user"]["name"] . '
';
        foreach($order["rest_order_object"]['rests_orders'][0]['order_detail'] as $item) {
          $msg .= $item['itemNameHe'] . '
';
            if ( array_key_exists('subItemsOneType', $item) ) {

                foreach( $item['subItemsOneType'] as $subItem ) {
                    foreach( $subItem as $key => $value) {
                        $msg .= $key  . ": " . $value["subItemNameHe"] . "
";
                    }
                }
            }

          if ( array_key_exists('multiItemsOneType', $item) ) {
            foreach( $item["multiItemsOneType"] as $itemOnType ) {
              $values = array_values($itemOnType);
              if ( !is_string($values[0]) ) {
                $msg .= $values[0]["subItemNameHe"] . "
";
              }
            }
          }
        }
        $msg .= "

";
       DB::useDB('orderapp_b2b_wui');
       DB::query("UPDATE b2b_orders SET sent=1 WHERE id = " . $order['order_id']);
      }

      $msg .= "------------------------

";

      $msg .= ' כמות סכו״ם ' . count($restaurant["orders"]) . '
';
      $msg .= "*נא לרשום את שם הלקוח על האריזה*" . '


';
$msg .= "------------------------
";
      $timings = $restaurant["orders"][0]["rest_order_object"]["rests_orders"][0]["selectedRestaurant"]["timings"];
      $msg .= "*שיהיה מוכן לאיסוף לשעה " . getTodaysPickupTime($timings) . "*";

      
      //SEND TELEGRAM FOR EVERY RESTAURANT
      telegramAPI($msg, $TEST_MODE);

      //SEND WHATSAPP MESSAGE
      $whatsapp_group_name = $restaurant["whatsapp_group_name"];
      $whatsapp_group_creator = $restaurant["whatsapp_group_creator"];
      whatsappAPI($whatsapp_group_creator, $whatsapp_group_name, $msg, $TEST_MODE);

      //SEND EMAIL TO RESTAURANT IF IT HAS ONE
      if ( $restaurant["email"] ) {
        sendOrderEmail($msg, $restaurant["email"]);
      }

      //SEND FAX TO RESTAURANT IF IT HAS ONE
      if ( $restaurant["fax_number"] ) {
        sendFax($restaurant["fax_number"], $msg, $TEST_MODE);
      }
    }
}

function sendDeliveryMsg($company, $restaurantsArray, $TEST_MODE) {

  $delivery_groups = array();
  $delivery_groups_id_array = array();
  $restaurants_delivery_data = getRestauranstDeliveryData($restaurantsArray);

  foreach( $restaurantsArray as $restaurant ) {

    $msg1 = "*הזמנה עסקי ל*" . $company["name"] . "
";
    $msg1 .= $company["delivery_address"] . "
";
    $msg1 .= "איש קשר " . $company["contact_number"] . "

";

    if ( $restaurants_delivery_data[$restaurant["id"]]["delivery_group"] == 0 ) {
      //$msg1 = $msg;
      $msg1 .= $restaurant["orders"][0]["rest_order_object"]["rests_orders"][0]["selectedRestaurant"]["name_en"] . '
';
      $msg1 .= "מנות " . count($restaurant["orders"]) . "

";
      $timings = $restaurant["orders"][0]["rest_order_object"]["rests_orders"][0]["selectedRestaurant"]["timings"];
      $msg1 .= "*האוכל יהיה מוכן בשעה " . getTodaysPickupTime($timings) . "*";

      //SEND WHATSAPP TO THE RESTAURANT
      whatsappAPI($restaurants_delivery_data[$restaurant["id"]]["whatsapp_group_creator"], $restaurants_delivery_data[$restaurant["id"]]["whatsapp_group_name"], $msg1, $TEST_MODE);

      //SEND TELEGRAM
      telegramAPI($msg1, $TEST_MODE);
      
    } else {

      $delivery_groups_new_item = array(
        "delivery_id" => $restaurants_delivery_data[$restaurant["id"]]["delivery_group"],
        "restaurants" => array(
          $restaurant
        ),
      );

      if ( count($delivery_groups) == 0 ) {
        array_push($delivery_groups, $delivery_groups_new_item);
        array_push($delivery_groups_id_array, $restaurants_delivery_data[$restaurant["id"]]["delivery_group"]);
      } else {
        foreach( $delivery_groups as &$delivery_group) {
          if ( $delivery_group["delivery_id"] == $delivery_groups_new_item["delivery_id"] ) {
            array_push($delivery_group["restaurants"], $restaurant);
            break;
          }
          else {
            array_push($delivery_groups, $delivery_groups_new_item);
            array_push($delivery_groups_id_array, $restaurants_delivery_data[$restaurant["id"]]["delivery_group"]);
            break;
          }
        }
      }
    }
  }


    $delivery_groups_data = getDeliveryGroupsData($delivery_groups_id_array);

    foreach( $delivery_groups as $delivery_group ) {

      $msg2 = "*הזמנה עסקי ל*" . $company["name"] . "
";
      $msg2 .= $company["delivery_address"] . "
";
      $msg2 .= "איש קשר " . $company["contact_number"] . "

";

      foreach( $delivery_group['restaurants'] as $restaurant ) {

        foreach( $restaurant['orders'] as $order ) {

          $msg2 .= $order["rest_order_object"]["rests_orders"][0]["selectedRestaurant"]["name_en"] . '
';
          $msg2 .= "מנות " . count($restaurant["orders"][0]["rest_order_object"]["rests_orders"][0]["order_detail"]) . "

";
        }
      }

      $timings = $restaurant["orders"][0]["rest_order_object"]["rests_orders"][0]["selectedRestaurant"]["timings"];
      $msg2 .= "*האוכל יהיה מוכן בשעה " . getTodaysPickupTime($timings) . "*";

      //SEND TELEGRAM
      telegramAPI($msg2, $TEST_MODE);

      //SEND WHATSAPP TO THE RESTAURANT
      whatsappAPI($delivery_groups_data[$delivery_group["delivery_id"]]["whatsapp_group_creator"], $delivery_groups_data[$delivery_group["delivery_id"]]["whatsapp_group_name"], $msg2, $TEST_MODE);

    }

}

function getDeliveryGroupsData($delivery_groups_id_array) {
    $delivery_groups_data = DB::query("SELECT `id`, `whatsapp_group_name`, `whatsapp_group_creator` FROM `delivery_groups` WHERE id IN (" . implode(",", $delivery_groups_id_array) . ")");

    $new_delivery_groups_data = array();

    foreach($delivery_groups_data as $delivery_item) {
      $new_delivery_groups_data[$delivery_item["id"]] = $delivery_item;
    }
    return $new_delivery_groups_data;
}

function getRestauranstDeliveryData($restaurantsArray) {
  $restaurants_ids = array();
  foreach( $restaurantsArray as $restaurant ) {
    array_push($restaurants_ids, $restaurant["id"]);
  }

  DB::useDB('orderapp_restaurants_b2b_wui');
  $delivery_data = DB::query("SELECT `id`, `delivery_group`, `whatsapp_group_name`, `whatsapp_group_creator` FROM `restaurants` WHERE id IN (" . implode(",", $restaurants_ids) . ")");

  $new_delivery_data = array();
  foreach($delivery_data as $data) {
      $new_delivery_data[$data["id"]] = $data;
    }
    return $new_delivery_data;

}

function getTodaysPickupTime($timing) {
  foreach ($timing as $day) {
    if ($day["week_en"] == date('l')) {
      return $readyForPickUpTime = $day["food_ready_for_pickup"];
    }
  }
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

//EMAIL FUNCTION
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

    

    $mail->Subject = 'New Orders';
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