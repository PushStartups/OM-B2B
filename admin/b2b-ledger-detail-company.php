<?php
include "header.php";
$company_id = $_GET['id'];

$company_name = getCompanyName($company_id)

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
                    <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-files-o "></i>&nbsp;<b><?=$company_name?></b> B2B Ledger</h1>
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
                                        <select class="form-control" id="company_select_id" style="color: black;" onchange="company_search(this.val(),'<?=$_SERVER['REQUEST_URI']?>')" >
                                            <option value=""  selected disabled> Select Company</option>
                                            <?php
                                            DB::useDB(B2B_DB);
                                            $company = DB::query("select * from company");
                                            foreach($company as $companies){  ?>
                                                <option value="<?=$companies['id']?>" ><?=$companies['name']?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </fieldset>
                    </form>
                </div>
            </div>
            <div id="swipe_row" class="row">
                <!-- NEW WIDGET START -->
                <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                    <!-- Widget ID (each widget will need unique ID)-->

                    <div class="jarviswidget" id="wid-id-2" data-widget-colorbutton="false" data-widget-editbutton="false">

                        <header>
                        </header>

                        <div>

                            <div class="jarviswidget-editbox">
                                <!-- This area used as dropdown edit box -->
                            </div>

                            <div class="widget-body">


                            </div>
                            <!-- end widget content -->
                        </div>
                        <!-- end widget div -->
                    </div>

                </article>
                <!-- WIDGET END -->
            </div>

            <br><br>
            <script>

            </script>
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
                                <h2><?=$company_name?> B2B Ledger Detail </h2>


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
                                            <th data-hide="phone,tablet">Delivery Price</th>
                                            <th data-hide="phone,tablet">Order No</th>
                                            <th data-hide="phone,tablet">Discount Amount</th>
                                            <th data-hide="phone,tablet">Restaurant Total</th>
                                            <th data-hide="phone, tablet">Customer Grand Total</th>
                                            <th data-hide="phone,tablet">Customer Total Paid To Restaurant</th>
                                            <th data-hide="phone,tablet">Company daily allowance</th>
                                            <th data-hide="phone,tablet">Company Total</th>
                                            <th data-hide="phone,tablet">Customer Pay</th>
                                            <th data-hide="phone,tablet">Action</th>
                                        </tr>
                                        </thead>

                                        <tbody id="target-content1">
                                        <?php DB::useDB(B2B_B2C_COMMON);
                                        $orders = DB::query("select * from b2b_ledger where company_name = '$company_name' order by id DESC");


                                        foreach ($orders as $order) {
                                            ?>

                                            <tr>
                                                <?php
                                                DB::useDB(B2B_DB);
                                                $company_info = DB::queryFirstRow("select * from company where name = '".$order['company_name']."'");
                                                $daily_allowance = $company_info['discount'];
                                                ?>

                                                <td><?= $order['date'] ?></td>
                                                <td><?= $order['time'] ?></td>
                                                <td><?= $order['customer_name'] ?></td>
                                                <td><?= $order['customer_contact'] ?></td>
                                                <td><?= $order['customer_email'] ?></td>
                                                <td><?= $order['company_name'] ?></td>
                                                <td><?= $order['restaurant_name'] ?></td>
                                                <td><?= $order['payment_method'] ?></td>
                                                <td><?= $order['delivery_or_pickup'] ?></td>
                                                <td><?= $order['delivery_price'] ?></td>
                                                <td><?= $order['order_no'] ?></td>
                                                <td><?= $order['discount_amount'] ?></td>
                                                <td><?= $order['restaurant_total'] ?></td>
                                                <td><?= $order['customer_grand_total'] ?></td>
                                                <td><?= $order['customer_total_paid_to_restaurant'] ?></td>
                                                <td><?= $daily_allowance ?></td>
                                                <?php
                                                //COMPANY TOTAL
                                                if($order['customer_grand_total'] >= $daily_allowance)
                                                { ?>
                                                    <td><?= $daily_allowance ?></td>
                                                <?php } else { ?>
                                                    <td><?= $order['customer_grand_total'] ?></td>
                                                <?php }


                                                if($order['customer_grand_total'] > $daily_allowance)
                                                {
                                                    $customer_pay = $order['customer_grand_total']- $daily_allowance;
                                                    ?>
                                                    <td><?= $customer_pay ?></td>
                                                <?php } else { ?>
                                                    <td><?= 0 ?></td>
                                                <?php }
                                                ?>
                                                <td><a href="edit-ledger.php?id=<?=$order['id']?>"><button class="btn btn-labeled btn-primary bg-color-blueDark txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-edit"></i> Edit </button></a></td>

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