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

                                        <th data-hide="phone, tablet">Payable Amount</th>
                                        <th data-hide="phone, tablet">Purchasing Amount</th>
                                        <th data-hide="phone,tablet">Todays's Remaining Balance</th>

                                        <th data-hide="phone, tablet">Refund</th>
                                        <th data-hide="phone, tablet">Transaction ID</th>



                                        <th data-hide="phone,tablet">Date</th>

                                        <th>Action</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php $orders = getAllB2BOrders();
                                    foreach ($orders as $order)
                                    {
                                        $refundAmount =   getTotalRefundAmountB2B($order['id']);
                                        ?>
                                        <tr>
                                            <td><?=$order['id']?></td>

                                            <td><?=$order['email']?></td>

                                            <td><?=$order['company_name']?></td>

                                            <td><?=$order['total']." NIS"?></td>

                                            <td><?=$order['actual_total']." NIS"?></td>

                                            <td><?=$order['discount']." NIS"?></td>

                                            <td><?=$refundAmount." NIS"?></td>

                                            <?php if(empty($order['transaction_id'])) { $order['transaction_id'] = "N/A"; }?>
                                            <td><?=$order['transaction_id']?></td>


                                            <td><?=$order['date']?></td>

                                            <td><a href="b2b-order-detail.php?order_id=<?=$order['id']?>"><button class="btn btn-labeled btn-primary bg-color-blueDark txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-info"></i> More Detail </button></a></td>
                                        </tr>
                                    <?php } ?>
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