<?php
session_start();
require_once '../inc/initDb.php';
require_once '../inc/functions.php';


$search_end_date = $_POST['end_date'];

$conditions = array();


$_SESSION['search_end_date'] = $search_end_date;

if($_SESSION['search_company'] != "") {
    $conditions[] = "o.company_id = '" . $_SESSION['search_company'] . "'";
}


if($_SESSION['search_email'] != "")
{
    $conditions[] = "o.user_id = '".$_SESSION['search_email']."'";
}


if(($_SESSION['search_start_date'] != "") && ($_SESSION['search_end_date'] == ""))
{
    $conditions[] = "o.date = '".$_SESSION['search_start_date']."'";
}


if(($_SESSION['search_end_date'] != "") && ($_SESSION['search_start_date'] == ""))
{
    $conditions[] = "o.date = '".$_SESSION['search_end_date']."'";
}

if(($_SESSION['search_end_date'] != "") && ($_SESSION['search_start_date'] != ""))
{
    $conditions[] = " DATE( o.date ) >= '".$_SESSION['search_start_date']."' AND DATE( o.date ) <= '".$_SESSION['search_end_date']."'";
}


DB::useDB('orderapp_b2b_wui');
$query = "select o.*, c.name as company_name, u.smooch_id as email from b2b_orders as o inner join company as c on o.company_id = c.id  inner join b2b_users as u on o.user_id = u.id";

$sql = $query;
$sql .= " WHERE " . implode(' AND ', $conditions);


$result = DB::query($sql);


$output = "";
foreach($result as $order) {

    $refundAmount = getTotalRefundAmountB2B($order['id']);
    DB::useDB('orderapp_restaurants_b2b_wui');
    $rest = DB::queryFirstRow("select * from restaurants where id = '".$order['restaurant_id']."' ");
    $restaurant_name = $rest['name_en'];


    $output .= '<tr>';
    $output .= '<td>' . $order['id'] . '</td>';
    $output .= '<td>' . $order['email'] . '</td>';

    $output .= '<td>' . $order['company_name'] . '</td>';
    $output .= '<td>' .  $restaurant_name.'</td>';

    $output .= '<td>' . $order['total'] . " NIS" . '</td>';

    $output .= '<td>' . $order['actual_total'] . " NIS" . '</td>';

    $output .= '<td>' . $order['discount'] . " NIS" . '</td>';
    $output .= '<td>' .  $order['payment_info'].'</td>';
    $output .= '<td>' . $refundAmount . " NIS" . '</td>';
    if (empty($order['transaction_id'])) {
        $order['transaction_id'] = "N/A";
    }
    $output .= '<td>' . $order['transaction_id'] . '</td>';

    $output .= '<td>' . $order['date'] . '</td>';

    $output .= '<td><a href="b2b-order-detail.php?order_id=' . $order['id'] . '"><button class="btn btn-labeled btn-primary bg-color-blueDark txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-info"></i> More Detail </button></a></td>';
    $output .= '</tr>';
}

echo json_encode($output);