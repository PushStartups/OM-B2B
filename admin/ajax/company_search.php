<?php
session_start();
require_once '../inc/initDb.php';
require_once '../inc/functions.php';


$company_id = $_POST['company_id'];
$conditions = array();


$_SESSION['search_company'] = $company_id;
$conditions[] = "o.company_id = '".$_SESSION['search_company']."'";


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


DB::useDB(B2B_DB);
$query = "select o.*, c.name as company_name, u.smooch_id as email from b2b_orders as o inner join company as c on o.company_id = c.id  inner join b2b_users as u on o.user_id = u.id";

$sql = $query;
$sql .= " WHERE " . implode(' AND ', $conditions);


$result = DB::query($sql);


$output = "";
$output .= '<tr><td></td><td></td><td></td><td></td><td></td><td style="width: 100%; padding:20px"><a href="ajax/b2bOrderDetaill.csv" download="b2bOrderDetaill.csv"  class="btn-lg btn-primary m-t-10" > Print CSV Report</a>
                                            </td> <td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
$file = fopen("b2bOrderDetaill.csv","w");
$list = array
(
    "Order ID,User Email"
);
foreach ($list as $line)
{
    fputcsv($file,explode(',',$line));
}
foreach($result as $order) {

    $refundAmount = getTotalRefundAmountB2B($order['id']);
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
    $output .= '<td>' .  $restaurant_name.'</td>';

    $output .= '<td>' . $order['total'] . " NIS" . '</td>';

    $output .= '<td>' . $order['actual_total'] . " NIS" . '</td>';

    $output .= '<td>' . $order['discount'] . " NIS" . '</td>';
    $output .= '<td>' . $order['company_contribution'] . " NIS" . '</td>';
    $output .= '<td>' .  $order['payment_info'].'</td>';
    $output .= '<td>' . $refundAmount . " NIS" . '</td>';
    if (empty($order['transaction_id'])) {
        $order['transaction_id'] = "N/A";
    }
    $output .= '<td>' . $order['transaction_id'] . '</td>';

    $output .= '<td>' . $order['date'] . '</td>';

    $output .= '<td><a href="b2b-order-detail.php?order_id=' . $order['id'] . '"><button class="btn btn-labeled btn-primary bg-color-blueDark txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-info"></i> More Detail </button></a></td>';
    $output .= '</tr>';
    fputcsv($file,$arr);

}
fclose($file);
echo json_encode($output);