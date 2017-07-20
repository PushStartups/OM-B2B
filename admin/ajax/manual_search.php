<?php
require_once '../inc/initDb.php';
require_once '../inc/functions.php';

session_start();
DB::query("set names utf8");

$user_email = $_POST['search-user-email'];
$company_id = $_POST['company_id'];
DB::useDB(B2B_DB);
$user  =    DB::queryFirstRow("select * from b2b_users where smooch_id = '$user_email'");
$user_id = $user['id'];


DB::useDB(B2B_DB);
$query = "select o.*, c.name as company_name, u.smooch_id as email from b2b_orders as o inner join company as c on o.company_id = c.id  
inner join b2b_users as u on o.user_id = u.id 
where DATE( o.date ) >= '".$_POST['search_start_date']."' 
AND DATE( o.date ) <= '".$_POST['search_end_date']."' AND o.user_id = '$user_id' AND o.company_id = '$company_id' ";






$result = DB::query($query);

$file = fopen("b2bOrderDetailSearch.csv","w");
$list = array
(
    "Order ID,User Email,Company,Restaurant Name,Total Paid,SubTotal,Discount,Company Contribution,Payment,Order Status,Transaction ID,Date Completed"
);
foreach ($list as $line)
{
    fputcsv($file,explode(',',$line));
}

$output = "";
$i = 1;
$totall = 0; $actual_total = 0 ; $discount = 0;
foreach($result as $order) {
    //$refundAmount = getTotalRefundAmountB2B($order['id']);
    DB::useDB(B2B_RESTAURANTS);
    $rest = DB::queryFirstRow("select * from restaurants where id = '".$order['restaurant_id']."' ");
    $restaurant_name = $rest['name_en'];
    $arr[] = "";

    $output .= '<tr>';
    $output .= '<td>' . $order['id'] . '</td>';
    $arr[0] = $order['id'];

    $output .= '<td>' . $order['email'] . '</td>';
    $arr[1] = $order['email'];

    $output .= '<td>' . $order['company_name'] . '</td>';
    $arr[2] = $order['company_name'];

    $output .= '<td>' .  $restaurant_name.'</td>';
    $arr[3] = $restaurant_name;

    $output .= '<td>' . $order['total'] . " NIS" . '</td>';
    $arr[4] = $order['total'];   $totall  = $totall + $order['total'];

    $output .= '<td>' . $order['actual_total'] . " NIS" . '</td>';
    $arr[5] = $order['actual_total'];   $actual_total  = $actual_total + $order['actual_total'];

    $output .= '<td>' . $order['discount'] . " NIS" . '</td>';
    $arr[6] = $order['discount'];   $discount  = $discount + $order['discount'];

    $output .= '<td>' . $order['company_contribution'] . " NIS" . '</td>';
    $arr[7] = $order['company_contribution'];

    $output .= '<td>' .  $order['payment_info'].'</td>';
    $arr[8] = $order['payment_info'];

    $output .= '<td>' .  $order['order_status'].'</td>';
    $arr[9] = $order['order_status'];

    if (empty($order['transaction_id'])) {
        $order['transaction_id'] = "N/A";
    }
    $output .= '<td>' . $order['transaction_id'] . '</td>';
    $arr[11] = $order['transaction_id'];

    $output .= '<td>' . $order['date'] . '</td>';
    $arr[12] = $order['date'];

    $output .= '<td><a href="b2b-order-detail.php?order_id=' . $order['id'] . '"><button class="btn btn-labeled btn-primary bg-color-blueDark txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-info"></i> More Detail </button></a></td>';
    $output .= '</tr>';


    $i++;
    fputcsv($file,$arr); }


$list = array
(
    ",,,Total :, $totall , $actual_total , $discount  "
);
foreach ($list as $line)
{
    fputcsv($file,explode(',',$line));
}

fclose($file);

echo json_encode($output);