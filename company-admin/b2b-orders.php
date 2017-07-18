<?php
include "header.php";
?>
    <!-- MAIN PANEL -->
    <div id="main" role="main">


        <!-- MAIN CONTENT -->
        <div id="content">

            <!-- row -->
            <div class="row">

                <!-- col -->
                <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                    <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-shopping-cart "></i><?=$_SESSION['company_name']?> Company Orders</h1>
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
            <!-- end row -->
            <div class ="row">
                <div class="col-xs-12">
                    <form  method="post" enctype="multipart/form-data">
                        <fieldset>

                            <div class="form-group">
                                <div class="row">

                                    <div class="col-xs-3">
                                        <input class="form-control" id="search_start_date" type="text" placeholder="Search Start Date">
                                    </div>
                                    <div class="col-xs-3">
                                        <input class="form-control" id="search_end_date"  type="text" placeholder="Search End Date">
                                    </div>
                                </div>
                            </div>

                        </fieldset>
                    </form>
                </div>
            </div>
            <br><br>
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
                                <h2><?=$_SESSION['company_name']?> Company Order Detail </h2>
                            </header>

                            <div align="center">
                                <br>
                                <a href="b2bCompanyOrderDetail.csv" download="b2bCompanyOrderDetail.csv"  class="btn-lg btn-primary m-t-10" > Print CSV Report</a>
                                <br>
                                <br>
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

                                    <?php  $file = fopen("b2bCompanyOrderDetail.csv","w");
                                    $list = array
                                    (
                                        "Order ID,User Email,Company,Total Paid,SubTotal,Today's Remaining Balance,Company Contribution,Transaction ID,Date Completed"
                                    );
                                    foreach ($list as $line)
                                    {
                                        fputcsv($file,explode(',',$line));
                                    }
                                    ?>

                                    <table id="datatable_tabletools" class="table table-striped table-bordered" width="100%">

                                        <thead>

                                        <tr>
                                            <th data-class="expand">Order ID</th>

                                            <th >User Email</th>

                                            <th data-hide="phone, tablet">Company</th>

                                            <th data-hide="phone, tablet">Total Paid</th>
                                            <th data-hide="phone, tablet">SubTotal</th>
                                            <th data-hide="phone,tablet">Todays's Remaining Balance</th>

                                            <th data-hide="phone,tablet">Company Contribution</th>


                                            <th data-hide="phone, tablet">Transaction ID</th>



                                            <th data-hide="phone,tablet">Date</th>

                                            <th>Action</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?php

                                        $i = 1;
                                        $totall = 0; $actual_total = 0 ; $discount = 0;

                                        DB::useDB('orderapp_b2b');
                                        $orders = DB::query("select o.*, c.name as company_name, u.smooch_id as email from b2b_orders as o inner join company as c on o.company_id = c.id  inner join b2b_users as u on o.user_id = u.id  where o.company_id = '".$_SESSION['company_id']."' order by o.id DESC");
                                        foreach ($orders as $order)
                                        {
                                            $refundAmount =   getTotalRefundAmountB2B($order['id']);
                                            $arr[] = "";
                                            ?>
                                            <tr>
                                                <td><?=$order['id']?></td>
                                                <?php  $arr[0] = $order['id'];  ?>

                                                <td><?=$order['email']?></td>
                                                <?php  $arr[1] = $order['email'];  ?>

                                                <td><?=$order['company_name']?></td>
                                                <?php  $arr[2] = $order['company_name'];  ?>

                                                <td><?=$order['total']." NIS"?></td>
                                                <?php  $arr[4] = $order['total'];   $totall  = $totall + $order['total']; ?>

                                                <td><?=$order['actual_total']." NIS"?></td>
                                                <?php  $arr[5] = $order['actual_total'];   $actual_total  = $actual_total + $order['actual_total']; ?>

                                                <td><?=$order['discount']." NIS"?></td>
                                                <?php  $arr[6] = $order['discount'];   $discount  = $discount + $order['discount']; ?>


                                                <?php if(empty($order['company_contribution'])) { $order['company_contribution'] = "N/A"; }?>
                                                <td><?=$order['company_contribution']?></td>
                                                <?php  $arr[7] = $order['company_contribution']; ?>

                                                <?php if(empty($order['transaction_id'])) { $order['transaction_id'] = "N/A"; }?>
                                                <td><?=$order['transaction_id']?></td>
                                                <?php  $arr[8] = $order['transaction_id'];  ?>

                                                <td><?=$order['date']?></td>
                                                <?php  $arr[9] = $order['date'];  ?>


                                                <td><a href="b2b-order-detail.php?order_id=<?=$order['id']?>"><button class="btn btn-labeled btn-primary bg-color-blueDark txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-info"></i> Detail </button></a></td>
                                            </tr>
                                            <?php $i++;
                                            fputcsv($file,$arr); }


                                        $list = array
                                        (
                                            ",,Total :, $totall NIS, $actual_total NIS, $discount NIS  "
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