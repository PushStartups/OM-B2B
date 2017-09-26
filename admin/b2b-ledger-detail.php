<?php
include "header.php";
$restaurant_id = $_GET['id'];

$rest_name = getRestaurantName($restaurant_id)

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
                    <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-files-o "></i>&nbsp;<b><?=$rest_name?></b> B2B Ledger</h1>
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
                                        <select class="form-control" id="rest_select_id" style="color: black;" onchange="rest_search(this.val(),'<?=$_SERVER['REQUEST_URI']?>')" >
                                            <option value=""  selected disabled> Select Restaurant</option>
                                            <?php
                                            DB::useDB(B2B_RESTAURANTS);
                                            $company = DB::query("select * from restaurants");
                                            foreach($company as $companies){  ?>
                                                <option value="<?=$companies['id']?>" ><?=$companies['name_en']?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-3">
                                        <select class="form-control" id="delivery_select_id" style="color: black;" onchange="delivery_search(this.val(),'<?=$_SERVER['REQUEST_URI']?>')" >
                                            <option value=""  selected disabled> Select Delivery Group</option>
                                            <?php
                                            DB::useDB(B2B_RESTAURANTS);
                                            $delivery = DB::query("select * from delivery_groups");
                                            foreach($delivery  as $deliveries){  ?>
                                                <option value="<?=$deliveries['delivery_team']?>" ><?=$deliveries['delivery_team']?></option>
                                            <?php } ?>

                                        </select>
                                    </div>
                                    <div class="col-xs-3">
                                        <select class="form-control" id="company_select_id" style="color: black;" onchange="company_search(this.val(),'<?=$_SERVER['REQUEST_URI']?>')" >
                                            <option value=""  selected disabled> Select Company</option>
                                            <?php
                                            DB::useDB(B2B_DB);
                                            $company = DB::query("select * from company");
                                            foreach($company as $companies){  ?>
                                                <option value="<?=$companies['name']?>" ><?=$companies['name']?></option>
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

                                <form>
                                    <fieldset>
                                        <input name="authenticity_token" type="hidden">
                                        <?php DB::useDB(B2B_RESTAURANTS);
                                        $orders = DB::queryFirstRow("select * from restaurants where name_en = '$rest_name'");
                                        ?>
                                        <div class="form-group">
                                            <label>Commission</label>
                                            <input class="form-control" id="delivery_commission" readonly name="delivery_commission" value="<?=$orders['comission']?>" type="text" required>

                                        </div>

                                        <div class="form-group">
                                            <label>Balance</label>
                                            <input class="form-control" id="delivery_balance" readonly name="delivery_balance" value="<?=$orders['balance']?>" placeholder="Enter Comments" type="text" required>

                                        </div>


                                    </fieldset>

                                </form>
                                <div id="swipe_div"  onclick="show_swipe_div()" class="btn btn-primary btn-lg">
                                    <i class="fa fa-plus"></i>
                                    Add Swipe
                                </div>&nbsp;&nbsp;<div id="swipe_div_go_back"  onclick="window.location.href='b2b-ledger.php'" class="btn btn-success btn-lg">
                                    <i class="fa fa-arrow-left"></i>
                                    Go Back
                                </div>
                                <br><br>
                                <div id="add-swipe" style="display: none">
                                    <form>
                                        <fieldset>
                                            <input name="authenticity_token" type="hidden">

                                            <input id="path1" name="editorImagePath1" type = "hidden" >



                                            <div class="form-group">
                                                <label>Amout Added Tab</label>
                                                <input class="form-control" id="amount_added_tab" name="amount_added_tab" placeholder="Enter Amount Added" type="text" required>
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="amount_added_tab_error"></span>
                                            </div>


                                            <div class="form-group">
                                                <label>Swiped By</label>
                                                <input class="form-control" id="swiped_by" name="swiped_by" placeholder="Enter Swiped By" type="text" required>
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="swiped_by_error"></span>
                                            </div>

                                            <div class="form-group">
                                                <label>Comments</label>
                                                <input class="form-control" id="comments" name="comments" placeholder="Enter Comments" type="text" required>
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="comments_error"></span>
                                            </div>


                                        </fieldset>
                                        <div class="form-actions">
                                            <div onclick="add_new_swipe('<?=$restaurant_id?>','<?=$_SERVER['REQUEST_URI']?>')" class="btn btn-primary btn-lg">
                                                <i class="fa fa-save"></i>
                                                Submit
                                            </div>
                                        </div>
                                    </form>
                                </div>

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
                                <h2><?=$rest_name?> B2B Ledger Detail </h2>


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
                                            <th data-class="expand">ID</th>
                                            <th data-class="expand">Date</th>
                                            <th >Time</th>
                                            <th data-hide="phone, tablet">Name</th>
                                            <th data-hide="phone, tablet">Contact</th>
                                            <th data-hide="phone, tablet">Email</th>
                                            <th data-hide="phone, tablet">Delivery Team</th>
                                            <th data-hide="phone, tablet">Company</th>
                                            <th data-hide="phone, tablet">Restaurant Name</th>
                                            <th data-hide="phone,tablet">Payment</th>
                                            <th data-hide="phone,tablet">Delivery Or Pickup</th>
                                            <th data-hide="phone,tablet">Order No</th>
                                            <th data-hide="phone,tablet">Balance</th>
                                            <th data-hide="phone,tablet">Restaurant Total</th>
                                            <th data-hide="phone, tablet">Customer Grand Total</th>
                                            <th data-hide="phone,tablet">Customer Total Paid To Restaurant</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>

                                        <tbody id="target-content1">
                                        <?php DB::useDB(B2B_DB);
                                        $orders = DB::query("select * from ledger where restaurant_name = '$rest_name' order by id DESC");


                                        foreach ($orders as $order) {
                                            ?>

                                            <tr>
                                                <td><?= $order['id'] ?></td>
                                                <td><?= $order['date'] ?></td>
                                                <td><?= $order['time'] ?></td>
                                                <td><?= $order['customer_name'] ?></td>
                                                <td><?= $order['customer_contact'] ?></td>
                                                <td><?= $order['customer_email'] ?></td>
                                                <td><?= $order['delivery_team'] ?></td>
                                                <td><?= $order['company_name'] ?></td>
                                                <td><?= $order['restaurant_name'] ?></td>
                                                <td><?= $order['payment_method'] ?></td>
                                                <td><?= $order['delivery_or_pickup'] ?></td>
                                                <td><?= $order['order_no'] ?></td>
                                                <td><?= $order['balance'] ?></td>
                                                <td><?= $order['restaurant_total'] ?></td>
                                                <td><?= $order['customer_grand_total'] ?></td>
                                                <td><?= $order['customer_total_paid_to_restaurant'] ?></td>
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



                <div class="row">
                    <!-- NEW WIDGET START -->
                    <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <!-- Widget ID (each widget will need unique ID)-->
                        <div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-2" data-widget-editbutton="false">


                            <header>
                                <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                                <h2><?=$rest_name?> B2C Swipe Detail </h2>


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



                                    <table id="dt_basic" class="table table-striped table-bordered" width="100%">


                                        <thead>


                                        <tr>
                                            <th style="display:none">#</th>
                                            <th data-class="expand">Amount Added Tab</th>
                                            <th >Date Added</th>
                                            <th data-hide="phone, tablet">Swiped By</th>
                                            <th data-hide="phone, tablet">Comments</th>

                                        </tr>

                                        </thead>

                                        <tbody>
                                        <?php
                                        DB::useDB('orderapp_restaurants');
                                        $order = DB::queryFirstRow("select * from restaurants where id = '$restaurant_id'");
                                        $tab1 = explode(",", $order['amount_added_tab']);
                                        $tab2 = explode(",", $order['date_added_tab']);
                                        $tab3 = explode(",", $order['swiped_by']);
                                        $tab4 = explode(",", $order['comments']);
                                        for ($i = 0; $i < sizeof($tab1); $i++)
                                        {
                                            ?>

                                            <tr>
                                                <td style="display:none"><?=$i+1;?></td>
                                                <td><?=$tab1[$i]?>  </td>
                                                <td><?=$tab2[$i]?>  </td>
                                                <td><?=$tab3[$i]?></td>
                                                <td><?=$tab4[$i]?> </td>
                                            </tr>
                                        <?php }
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