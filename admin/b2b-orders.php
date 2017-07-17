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
                <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                    <form  method="post" enctype="multipart/form-data">
                        <fieldset>

                            <div class="form-group">
                                <select class="form-control"  onchange="search_company(this.value);">
                                    <option value=""  selected disabled> Select Company</option>
                                    <?php
                                    DB::useDB('orderapp_b2b_wui');
                                    $company = DB::query("select * from company");
                                    foreach($company as $companies){  ?>
                                        <option value=<?=$companies['id']?>><?=$companies['name']?></option>
                                    <?php } ?>

                                </select>
                            </div>


                            <div class="form-group">
                                <input class="form-control" id="search-user-email" type="text" placeholder="Search User Email"><br>
                            </div>

                            <div class="form-group">
                                <input class="form-control" id="search-start-date" type="text" placeholder="Search Start Date"><br>
                            </div>

                            <div class="form-group">
                                <input class="form-control" id="search-end-date"  type="text" placeholder="Search End Date"><br>
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
                            <div align="center">
                                <a href="b2bOrderDetail.csv" download="b2bOrderDetail.csv"  class="btn-lg btn-primary m-t-10" > Export CSV File</a>
                            </div>
                            <!-- widget div-->
                            <div>
                                <!-- widget edit box -->
                                <div class="jarviswidget-editbox">
                                    <!-- This area used as dropdown edit box -->
                                </div>
                                <!-- end widget edit box -->
                                <!-- widget content -->
                                <div class="widget-body no-padding">

                                    <?php  $file = fopen("b2bOrderDetail.csv","w");
                                    $list = array
                                    (
                                        "Order ID,User Email,Company,Restaurant Name,Payable Amount,Purchasing Amount,Today's Remaining Balance,Payment,Refund,Transaction ID,Date Completed"
                                    );
                                    foreach ($list as $line)
                                    {
                                        fputcsv($file,explode(',',$line));
                                    }
                                    ?>

                                    <table id="datatable_fixed_column" class="table table-striped table-bordered" width="100%">

                                        <thead>

                                        <tr>
                                            <th data-class="expand">Order ID</th>

                                            <th >User Email</th>

                                            <th data-hide="phone, tablet">Company</th>
                                            <th data-hide="phone, tablet">Restaurant Name</th>

                                            <th data-hide="phone, tablet">Payable Amount</th>
                                            <th data-hide="phone, tablet">Purchasing Amount</th>
                                            <th data-hide="phone,tablet">Today's Remaining Balance</th>
                                            <th data-hide="phone,tablet">Payment</th>

                                            <th data-hide="phone, tablet">Refund</th>
                                            <th data-hide="phone, tablet">Transaction ID</th>



                                            <th data-hide="phone,tablet">Date</th>

                                            <th>Action</th>
                                        </tr>
                                        </thead>

                                        <tbody id="target-content">
                                        <?php $orders = getAllB2BOrders();
                                        $i = 1;
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
                                                <?php  $arr[4] = $order['total'];  ?>

                                                <td><?=$order['actual_total']." NIS"?></td>
                                                <?php  $arr[5] = $order['actual_total'];  ?>

                                                <td><?=$order['discount']." NIS"?></td>
                                                <?php  $arr[6] = $order['discount'];  ?>

                                                <td><?=$order['payment_info']?></td>
                                                <?php  $arr[7] = $order['payment_info'];  ?>

                                                <td><?=$refundAmount." NIS"?></td>
                                                <?php  $arr[8] = $refundAmount."NIS";  ?>

                                                <?php if(empty($order['transaction_id'])) { $order['transaction_id'] = "N/A"; }?>
                                                <td><?=$order['transaction_id']?></td>
                                                <?php  $arr[9] = $order['transaction_id'];  ?>



                                                <td><?=$order['date']?></td>
                                                <?php  $arr[10] = $order['date'];  ?>

                                                <td><a href="b2b-order-detail.php?order_id=<?=$order['id']?>"><button class="btn btn-labeled btn-primary bg-color-blueDark txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-info"></i> More Detail </button></a></td>
                                            </tr>

                                            <?php $i++;
                                            fputcsv($file,$arr); } ?>


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