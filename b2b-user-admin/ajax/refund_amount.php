<?php
require_once '../inc/initDb.php';

$order_id          =   $_POST['order_id'];
$transaction_id         =   $_POST['transaction_id'];
$refund_amount_NIS =   $_POST['refund_amount'];
$refund_amount     =   $_POST['refund_amount'] * 100;
$success = 0;

//CREDIT GUARD PAYMENT DETAIL


$cgConf['tid']='0962922';
$cgConf['amount'] = $refund_amount;
$cgConf['user']='israel';
$cgConf['password']='Israeli1.';
$cgConf['cg_gateway_url']="https://cguat2.creditguard.co.il/xpo/Relay";

$poststring = 'user='.$cgConf['user'];
$poststring .= '&password='.$cgConf['password'];





/*Build Ashrait XML to post*/
$poststring.='&int_in=<ashrait>
<request>
<command>refundDeal</command>
<requesteId>123</requesteId>
<dateTime/>
<version>1001</version>
<language>Eng</language>
<refundDeal>
<terminalNumber>'.$cgConf['tid'].'</terminalNumber>
<tranId>'.$transaction_id .'</tranId>
<total>'.$cgConf['amount'].'</total>
</refundDeal>
</request>
</ashrait>';


//init curl connection
if( function_exists( "curl_init" )) {
    $CR = curl_init();
    curl_setopt($CR, CURLOPT_URL, $cgConf['cg_gateway_url']);
    curl_setopt($CR, CURLOPT_POST, 1);
    curl_setopt($CR, CURLOPT_FAILONERROR, true);
    curl_setopt($CR, CURLOPT_POSTFIELDS, $poststring);
    curl_setopt($CR, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($CR, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($CR, CURLOPT_FAILONERROR,true);


//actual curl execution perfom
    $result = curl_exec( $CR );
    $error = curl_error ( $CR );

// on error - die with error message
    if( !empty( $error )) {
        die($error);
    }

    $dom = new DOMDocument;
    $dom->loadXML($result);


    $items     = $dom->getElementsByTagName('response')->item(0);

    //print_r($items->childNodes);

    foreach($items->childNodes as $item){

        if($item->tagName == 'result') {

           $str = $item->nodeValue . "\n";
            if(intval($str) == 0)
            {
                //RREFUND SUCCESS
                DB::query("UPDATE user_orders SET total = total - '$refund_amount_NIS' WHERE id = '$order_id'");
                DB::insert('refund', array(
                    'order_id' => $order_id,
                    'amount'   => $refund_amount_NIS
                ));
                $success = 1;


            }
            else{
                $success = $item->nodeValue;
            }
        }

    }


    curl_close($CR);
    //print_r($result);


}
if($success == 1)
{
    echo json_encode("success");
}
else{
    echo json_encode($success);
}
?>