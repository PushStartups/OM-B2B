<?php
include "header.php";
?>
    <style>
        .csv_hide{
            display: none;
        }
    </style>
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
                                <h2>B2B Ledger Detail </h2>


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
                                            <th data-class="expand">Date</th>

                                            <th >Time</th>

                                            <th data-hide="phone, tablet">Name</th>
                                            <th data-hide="phone, tablet">Contact</th>
                                            <th data-hide="phone, tablet">Email</th>
                                            <th data-hide="phone, tablet">Company Name</th>
                                            <th data-hide="phone, tablet">Restaurant Name</th>
                                            <th data-hide="phone,tablet">Payment</th>
                                            <th data-hide="phone,tablet">Delivery Or Pickup</th>
                                            <th data-hide="phone,tablet">Order No</th>
                                            <th data-hide="phone,tablet">Restaurant Total</th>
                                            <th data-hide="phone, tablet">Customer Grand Total</th>
                                            <th data-hide="phone,tablet">Customer Total Paid To Restaurant</th>
                                        </tr>
                                        </thead>

                                        <tbody id="target-content">
                                        <?php DB::useDB(B2B_DB);
                                        $orders = DB::query("select * from b2b_ledger");


                                        foreach ($orders as $order) {
                                            ?>

                                            <tr>
                                                <td><?= $order['date'] ?></td>
                                                <td><?= $order['time'] ?></td>
                                                <td><?= $order['customer_name'] ?></td>
                                                <td><?= $order['customer_contact'] ?></td>
                                                <td><?= $order['customer_email'] ?></td>
                                                <td><?= $order['company_name'] ?></td>
                                                <td><?= $order['restaurant_name'] ?></td>
                                                <td><?= $order['payment_method'] ?></td>
                                                <td><?= $order['delivery_or_pickup'] ?></td>
                                                <td><?= $order['order_no'] ?></td>
                                                <td><?= $order['restaurant_total'] ?></td>
                                                <td><?= $order['customer_grand_total'] ?></td>
                                                <td><?= $order['customer_total_paid_to_restaurant'] ?></td>
                                            </tr>

                                            <?php
                                        }


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