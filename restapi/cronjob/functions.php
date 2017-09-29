<?php

require_once (dirname(__FILE__) . '/../Interfax/vendor/autoload.php');
require_once (dirname(__FILE__) . '/../vendor/autoload.php');
use Mailgun\Mailgun;
use Interfax\Client;
use Stichoza\GoogleTranslate\TranslateClient;

//TRANSLATION SET UP
$tr = new TranslateClient();
$tr->setTarget('iw');

//GET ARRAY OF ALL COMPANIES AND THEIR TIMINGS
function getCompanies($TEST_MODE) {
  $timings = DB::query("SELECT company_id, week_en, closing_time, food_ready_for_pickup FROM company_timing");
  //echo '<pre>'; var_dump($timings); echo '</pre>';
  if ( $TEST_MODE ) {
    $companies = DB::query("SELECT id, name, delivery_address, contact_number FROM company WHERE id = 2");
    foreach ( $companies as &$company) {
      $timing = getTimingForCompany($timings, 2);
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
            $today = date('l');
            if ( $timing["week_en"] == $today && isSendTime($timing["closing_time"]) ) {
              array_push($returnCompanies, $company);
              //echo '<pre>'; var_dump($company); echo '</pre>';
              break;
            }
          }
        }
      }
      
      return $returnCompanies;
  }
}

//COMPARE THE SEND TIME AND CURRENT TIME
function isSendTime($closing_time) {
  if ( count($closing_time) != 0 && $closing_time != 'Closed' && !is_null($closing_time) ) {
    $closing_time_array = explode(":", $closing_time);
    $closing_hour = $closing_time_array[0];
    $closing_minutes = $closing_time_array[1];
    
    $current_hour = date('H');
    $current_minutes = date('i');

    if( $current_hour == $closing_hour){
      if( $current_minutes >= $closing_minutes && $current_minutes - $closing_minutes < 3 ){
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
    //echo '<pre>'; var_dump($restaurantsArray); echo '</pre>';

  }
}

//RETURN ARRAY OF RESTAURANTS WITH THEIR ID AND ORDERS
// ARRAY => ( ID => %NUMBER%, ORDER => ARRAY() )
function getOrdersByRestaurant($ordersFromDB) {
  $restaurantsArray = array();

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
  
    //ADD WHATSAPP GROUPS INFO TO THE RESTAURANTS ARRAYS
    $restaurantsIds = array();
    foreach( $restaurantsArray as $restaurant) {
      array_push($restaurantsIds, $restaurant["id"]);
    }

    DB::useDB('orderapp_restaurants_b2b_wui');
    $additional_rest_data = DB::query("SELECT `id`, `delivery_group`, `whatsapp_group_name` , `whatsapp_group_creator`, `fax_number` , `email`, `firebase_chat_id` FROM `restaurants` WHERE  `id` IN (". implode(",", $restaurantsIds) . ")");
    

    foreach($restaurantsArray as &$restaurant) {
      foreach( $additional_rest_data as $item ) {
        if ( $restaurant["id"] == $item["id"] ) {
          $restaurant["delivery_group"] = $item["delivery_group"];
          $restaurant["whatsapp_group_name"] = $item["whatsapp_group_name"];
          $restaurant["whatsapp_group_creator"] = $item["whatsapp_group_creator"];
          $restaurant["fax_number"] = $item["fax_number"];
          $restaurant["email"] = $item["email"];
          $restaurant["firebase_chat_id"] = $item["firebase_chat_id"];
        }
      }
    }
  
    return $restaurantsArray;
}

//ASSEMBLE AND SEND MESSAGE FOR RESTAURANT
function sendOrderMessage($restaurantsArray, $TEST_MODE) {

  foreach( $restaurantsArray as $restaurant ) {
      
      //SEND TELEGRAM FOR EVERY RESTAURANT
      telegramAPI(createMessage($restaurant), $TEST_MODE);

      //SEND WHATSAPP MESSAGE
      $whatsapp_group_name = $restaurant["whatsapp_group_name"];
      $whatsapp_group_creator = $restaurant["whatsapp_group_creator"];
      whatsappAPI($whatsapp_group_creator, $whatsapp_group_name, createMessage($restaurant), $TEST_MODE);

      //SEND EMAIL TO RESTAURANT IF IT HAS ONE
      if ( $restaurant["email"] ) {
        sendEmail(emailMessage($restaurant), $restaurant["email"]);
      }

      //SEND FAX TO RESTAURANT IF IT HAS ONE
      if ( $restaurant["fax_number"] ) {
        sendFax($restaurant["fax_number"], createMessage($restaurant), $TEST_MODE);
      }
  
      if( ($restaurant["firebase_chat_id"] && !is_null($restaurant["firebase_chat_id"])) || $TEST_MODE ) {
        firebaseMsg($restaurant["firebase_chat_id"], createMessage($restaurant), $TEST_MODE);
      }
      
      
    }
}

//SENDS DELIVERY MESSAGE
function sendDeliveryMsg($company, $restaurantsArray, $TEST_MODE) {

  $delivery_groups = array();
  $delivery_groups_id_array = array();
  $restaurants_delivery_data = getRestauranstDeliveryData($restaurantsArray);

  foreach( $restaurantsArray as $restaurant ) {

    $msg1 = "הזמנה ללקוח עסקי:  " . $company["name"] . "
";
    $msg1 .= "כתובת: " . $company["delivery_address"] . "
";
    $msg1 .= "איש קשר : " . $company["contact_number"] . "

";
    
    if ( $restaurants_delivery_data[$restaurant["id"]]["delivery_group"] == "0" ) {
      //$msg1 = $msg;
      $msg1 .= "ממסעדה: " .$restaurant["orders"][0]["rest_order_object"]["rests_orders"][0]["selectedRestaurant"]["name_en"] . '
';
      $msg1 .= "כמות מנות " . count($restaurant["orders"]) . "

";
      $timings = $restaurant["orders"][0]["rest_order_object"]["rests_orders"][0]["selectedRestaurant"]["timings"];
      $msg1 .= "האוכל יהיה מוכן בשעה " . getTodaysPickupTime($timings) . "";

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
    
    if ( count($delivery_groups) > 0 ) {
      $delivery_groups_data = getDeliveryGroupsData($delivery_groups_id_array);
  
      foreach( $delivery_groups as $delivery_group ) {
    
        $msg2 = "*הזמנה ללקוח עסקי: *" . $company["name"] . "
";
        $msg2 .= $company["delivery_address"] . "
";
        $msg2 .= "איש קשר : " . $company["contact_number"] . "

";
    
        foreach( $delivery_group['restaurants'] as $restaurant ) {
      
          foreach( $restaurant['orders'] as $order ) {
        
            $msg2 .= $order["rest_order_object"]["rests_orders"][0]["selectedRestaurant"]["name_en"] . '
';
            $msg2 .= "כמות מנות " . count($restaurant["orders"][0]["rest_order_object"]["rests_orders"][0]["order_detail"]) . "

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
}

//CREATE MESSAGE FOR TELEGRAM, WHATSAPP, FAX
function createMessage($restaurant) {
  global $tr;
  $msg = '';
  $msg .= $restaurant["orders"][0]["rest_order_object"]["rests_orders"][0]["selectedRestaurant"]["name_he"] . '

';
  $msg .= 'הזמנה ללקוח עסקי' . '
';
  $msg .= $restaurant["orders"][0]["rest_order_object"]["company"]["company_name"]. '
';
  $msg .= '------------------------

';
  foreach( $restaurant['orders'] as $order ) {
    
    $msg .= 'שם : ' . $order["rest_order_object"]["user"]["name"] . '
';
    foreach($order["rest_order_object"]['rests_orders'][0]['order_detail'] as $item) {
      $msg .= $item['itemNameHe'] . '
';
      if ( array_key_exists('subItemsOneType', $item) ) {
        
        foreach( $item['subItemsOneType'] as $subItem ) {
          foreach( $subItem as $key => $value) {
            $msg .= $tr->translate($key)  . ': ' . $value["subItemNameHe"] . '
';
          }
        }
      }
      
      if ( array_key_exists('multiItemsOneType', $item) ) {
        foreach( $item["multiItemsOneType"] as $itemOnType ) {
          $values = array_values($itemOnType);
          if ( !is_string($values[0]) ) {
            $msg .= $values[0]["subItemNameHe"] . '
';
          }
        }
      }
    }
    $msg .= '

';
    DB::useDB('orderapp_b2b_wui');
    DB::query("UPDATE b2b_orders SET sent=1 WHERE id = " . $order['order_id']);
  }
  
  $msg .= '------------------------

';
  
  $msg .= ' כמות סכו״ם ' . count($restaurant["orders"]) . '
';
  $msg .= '*נא לרשום את שם הלקוח על האריזה*' . '


';
  $msg .= '------------------------
';
  $timings = $restaurant["orders"][0]["rest_order_object"]["rests_orders"][0]["selectedRestaurant"]["timings"];
  $msg .= '*שיהיה מוכן לאיסוף לשעה ' . getTodaysPickupTime($timings) . '*';
  
  return $msg;
}

//CREATE HTML MESSAGE FOR EMAIL
function emailMessage($restaurant) {
  global $tr;
  $msg = '<html>';
  $msg .= '<body dir="rtl">';
  
  $msg .= '<p>' . $restaurant["orders"][0]["rest_order_object"]["rests_orders"][0]["selectedRestaurant"]["name_he"] . '</p>';
  $msg .= '<br>';
  $msg .= '<p>' . 'הזמנה ללקוח עסקי' . '</p>';
  $msg .= '<p>' . $restaurant["orders"][0]["rest_order_object"]["company"]["company_name"]. '</p>';
  $msg .= '<div>' . '------------------------' . '</div>';
  $msg .= '<br>';
  
  foreach( $restaurant['orders'] as $order ) {
    
    $msg .= '<p>' . 'שֵׁם : ' . $order["rest_order_object"]["user"]["name"] . '</p>';
    foreach($order["rest_order_object"]['rests_orders'][0]['order_detail'] as $item) {
      
      $msg .= '<p>' . $item['itemNameHe'] . '</p>';
      
      if ( array_key_exists('subItemsOneType', $item) ) {
        foreach( $item['subItemsOneType'] as $subItem ) {
          foreach( $subItem as $key => $value) {
            $msg .= '<p>' . $tr-translate($key)  . ": " . $value["subItemNameHe"] . '</p>';
          }
        }
      }
      
      if ( array_key_exists('multiItemsOneType', $item) ) {
        foreach( $item["multiItemsOneType"] as $itemOnType ) {
          $values = array_values($itemOnType);
          if ( !is_string($values[0]) ) {
            $msg .= '<p>' . $values[0]["subItemNameHe"] . '</p>';
          }
        }
      }
    }
    $msg .= '<br>';
    DB::useDB('orderapp_b2b_wui');
    DB::query("UPDATE b2b_orders SET sent=1 WHERE id = " . $order['order_id']);
  }
  
  $msg .= '<div>' . '------------------------' . '</div>';
  $msg .= '<br>';
  
  $msg .= '<p>' . ' כמות סכו״ם ' . count($restaurant["orders"]) . '</p>';
  $msg .= '<p>'. '*נא לרשום את שם הלקוח על האריזה*' . '</p>';
  $msg .= '<br>';
  $msg .= '<br>';
  $msg .= '<div>'.'------------------------'.'</div>';
  $msg .= '<br>';
  $timings = $restaurant["orders"][0]["rest_order_object"]["rests_orders"][0]["selectedRestaurant"]["timings"];
  $msg .= '<p>' . '*שיהיה מוכן לאיסוף לשעה ' . getTodaysPickupTime($timings) . '*' . '</p>';
  $msg .= '</body>';
  $msg .= '</html>';
  
  return $msg;
}

function getDeliveryGroupsData($delivery_groups_id_array) {
  
    DB::useDB(B2B_B2C_COMMON);
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

function sendEmail($msg, $toEmail) {
  
  if($_SERVER['HTTP_HOST'] == 'eluna.orderapp.com')
  {
    //$subject = "(ELUNA) "+$user_order['restaurantTitle'].' Order# '.$orderId;
  }
  
  else
  {
    $subject = 'New Orders';
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

//HANDLES FIREBASE MESSAGES
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

//SENDS MESSAGES
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

//UPDATES THE FIREBASE AFTER THE MESSAGE WAS SENT
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