<?php
include "header.php";
$_SESSION['search_email'] = "";
$_SESSION['search_company'] = "";
$_SESSION['search_start_date'] = "";
$_SESSION['search_end_date'] = "";
?>
    <!-- MAIN PANEL -->
    <div id="main" role="main">


        <!-- MAIN CONTENT -->
        <div id="content">

            <!-- row -->
            <div class="row">

                <!-- col -->
                <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                    <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-shopping-cart "></i>B2B Orders</h1>
                </div>
                <!-- end col -->

                <!-- right side of the page with the sparkline graphs -->
                <!-- col -->
                <div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">
                    <!-- sparks -->

                    <!-- end sparks -->
                </div>
                <!-- end col -->

            </div>
            <div class ="row">
                <div class="col-xs-12">
                    <form  method="post" enctype="multipart/form-data">
                        <fieldset>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <select class="form-control" id="company_id" style="color: black;" >
                                            <option value=""  selected disabled> Select Company</option>
                                            <?php
                                            DB::useDB('orderapp_b2b_wui');
                                            $company = DB::query("select * from company");
                                            foreach($company as $companies){  ?>
                                                <option value="<?=$companies['id']?>" ><?=$companies['name']?></option>
                                            <?php } ?>

                                        </select>
                                        <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_company_id"></span>

                                    </div>
                                    <div class="col-xs-3">
                                        <input class="form-control" id="search-user-email" type="text" placeholder="Search User Email">
                                        <input class="form-control" id="hidden_email" type="hidden" >
                                        <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_search_email"></span>
                                    </div>
                                    <div class="col-xs-2">
                                        <input class="form-control" id="search_start_date" type="text" placeholder="Search Start Date">
                                        <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_search_start_date"></span>
                                    </div>
                                    <div class="col-xs-2">
                                        <input class="form-control" id="search_end_date"  type="text" placeholder="Search End Date">
                                        <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_search_end_date"></span>
                                    </div>
                                    <div class="col-xs-2">
                                        <button  onclick="insert_b2b_orders_date('<?=$_SERVER['REQUEST_URI']?>')"  type="button" class="btn-lg btn-primary m-t-10" style="padding: 2px 16px;">Search</button>
                                    </div>
                                </div>
                            </div>

                        </fieldset>
                    </form>
                </div>
            </div>
            <br><br>
            <!-- end row -->

            <!--
            The ID "widget-grid" will start to initialize all widgets below
            You do not need to use widgets if you dont want to. Simply remove
            the <section></section> and you can use wells or panels instead  -->

            <!-- widget grid -->
            <section id="widget-grid" class="">
                <!-- row -->
                <div class="row">
                    <!-- NEW WIDGET START -->
                    <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <!-- Widget ID (each widget will need unique ID)-->
                        <div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-1" data-widget-editbutton="false">

                            <header>
                                <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                                <h2>B2B Order Detail </h2>
                            </header>

                            <!-- widget div-->
                            <div>
                                <!-- widget edit box -->
                                <div class="jarviswidget-editbox">
                                    <!-- This area used as dropdown edit box -->
                                </div>
                                <!-- end widget edit box -->
                                <!-- widget content -->
                                <div class="widget-body no-padding">



                                    <table id="datatable_fixed_column" class="table table-striped table-bordered" width="100%">

                                        <thead>

                                        <tr>
                                            <th data-class="expand">Order ID</th>

                                            <th >User Email</th>

                                            <th data-hide="phone, tablet">Company</th>
                                            <th data-hide="phone, tablet">Restaurant Name</th>

                                            <th data-hide="phone, tablet">Total Paid</th>
                                            <th data-hide="phone, tablet">SubTotal</th>
                                            <th data-hide="phone,tablet">Today's Remaining Balance</th>
                                            <th data-hide="phone,tablet">Company Contribution</th>
                                            <th data-hide="phone,tablet">Payment</th>

                                            <th data-hide="phone, tablet">Refund</th>
                                            <th data-hide="phone, tablet">Transaction ID</th>



                                            <th data-hide="phone,tablet">Date</th>

                                            <th>Action</th>
                                        </tr>
                                        </thead> 

                                        <tbody id="target-content">

                                        <tr>
                                            <td></td><td></td><td></td><td></td><td></td><td style="width: 100%; padding:20px"><a href="b2bOrderDetail.csv" download="b2bOrderDetail.csv"  class="btn-lg btn-primary m-t-10" > Print CSV Report</a>
                                            </td> <td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                        <?php  $file = fopen("b2bOrderDetail.csv","w");
                                        $list = array
                                        (
                                            "Order ID,User Email,Company,Restaurant Name,Total Paid,SubTotal,Today's Remaining Balance,Company Contribution,Payment,Refund,Transaction ID,Date Completed"
                                        );
                                        foreach ($list as $line)
                                        {
                                            fputcsv($file,explode(',',$line));
                                        }
                                        ?>
                                        <?php $orders = getAllB2BOrders();
                                        $i = 1;
                                        $totall = 0; $actual_total = 0 ; $discount = 0;
                                        foreach ($orders as $order)
                                        {
                                            $refundAmount =   getTotalRefundAmountB2B($order['id']);
                                            DB::useDB('orderapp_restaurants_b2b_wui');
                                            $rest = DB::queryFirstRow("select * from restaurants where id = '".$order['restaurant_id']."' ");
                                            $restaurant_name = $rest['name_en'];
                                            $arr[] = "";
                                            ?>

                                            <tr>
                                                <td><?=$order['id']?></td>
                                                <?php  $arr[0] = $order['id'];  ?>

                                                <td><?=$order['email']?></td>
                                                <?php  $arr[1] = $order['email'];  ?>

                                                <td><?=$order['company_name']?></td>
                                                <?php  $arr[2] = $order['company_name'];  ?>

                                                <td><?=$restaurant_name?></td>
                                                <?php  $arr[3] = $restaurant_name;  ?>

                                                <td><?=$order['total']." NIS"?></td>
                                                <?php  $arr[4] = $order['total'];   $totall  = $totall + $order['total']; ?>

                                                <td><?=$order['actual_total']." NIS"?></td>
                                                <?php  $arr[5] = $order['actual_total'];   $actual_total  = $actual_total + $order['actual_total']; ?>

                                                <td><?=$order['discount']." NIS"?></td>
                                                <?php  $arr[6] = $order['discount'];   $discount  = $discount + $order['discount']; ?>


                                                <td><?=$order['company_contribution']." NIS"?></td>
                                                <?php  $arr[7] = $order['company_contribution']; ?>


                                                <td><?=$order['payment_info']?></td>
                                                <?php  $arr[8] = $order['payment_info'];  ?>

                                                <td><?=$refundAmount." NIS"?></td>
                                                <?php  $arr[9] = $refundAmount."NIS";  ?>

                                                <?php if(empty($order['transaction_id'])) { $order['transaction_id'] = "N/A"; }?>
                                                <td><?=$order['transaction_id']?></td>
                                                <?php  $arr[10] = $order['transaction_id'];  ?>



                                                <td><?=$order['date']?></td>
                                                <?php  $arr[11] = $order['date'];  ?>

                                                <td><a href="b2b-order-detail.php?order_id=<?=$order['id']?>"><button class="btn btn-labeled btn-primary bg-color-blueDark txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-info"></i> More Detail </button></a></td>
                                            </tr>

                                            <?php $i++;
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

                                        ?>


                                        </tbody>

                                    </table>

                                </div>
                                <!-- end widget content -->

                            </div>
                            <!-- end widget div -->

                        </div>
                        <!-- end widget -->

                    </article>
                    <!-- WIDGET END -->

                </div>

                <!-- end row -->

                <!-- row -->

                <div class="row">

                    <!-- a blank row to get started -->
                    <div class="col-sm-12">
                        <!-- your contents here -->
                    </div>

                </div>

                <!-- end row -->

            </section>

            <!-- end widget grid -->

        </div>
        <!-- END MAIN CONTENT -->

    </div>
    <!-- END MAIN PANEL -->
<?php
include "footer.php";
?>