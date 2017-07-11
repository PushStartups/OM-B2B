<?php
$bot_id = "234472538:AAEwJUUgl0nasYLc3nQtGx4N4bzcqFT-ONs";
$chat_id = "-165732759";

telegramAPI($bot_id, $chat_id, 'testing');

function telegramAPI($bot_id, $chat_id, $text) {


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
    echo "Response: ".$response;
    curl_close($ch);


}