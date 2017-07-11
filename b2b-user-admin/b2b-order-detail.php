<?php
include "header.php";
?>
<!-- MAIN PANEL -->
<?php
if(isset($_GET['order_id'])){
    $order_id = $_GET['order_id'];
}
else{
    header("location:logout.php");
}
?>
<div id="main" role="main">


    <!-- MAIN CONTENT -->
    <div id="content">

        <!-- row -->
        <div class="row">

            <!-- col -->
            <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                <?php $order = getCompanyNameByOrderId($order_id) ?>
                <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-cutlery "></i><?=$order['company_name']?></h1>
            </div>
            <!-- end col -->

            <!-- right side of the page with the sparkline graphs -->
            <!-- col -->
            <div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">
                <!-- sparks -->
                <ul id="sparks">

                </ul>
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
                            <h2>Order Detail </h2>

                        </header>

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
                                        <th data-class="expand">Restaurant Name</th>
                                        <th >Item</th>
                                        <th data-hide="phone, tablet">Sub-Total</th>
                                        <th data-hide="phone, tablet">Sub-Items</th>
                                        <th data-hide="phone, tablet">Quantity</th>

                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php $orders = getOrderItemsB2B($order_id);
                                    foreach ($orders as $order)
                                    {
                                        ?>
                                        <tr>
                                            <td><?=$order['order_id']?></td>
                                            <?php
                                            DB::useDB('orderapp_b2b');
                                            $restaurant_id = DB::queryFirstRow("select * from b2b_orders where id = '".$order['order_id']."'");
                                            //echo $order['order_id'];
                                            DB::useDB('orderapp_restaurants');
                                            $restaurant    = DB::queryFirstRow("select * from restaurants where id = '".$restaurant_id['restaurant_id']."'");

                                            ?>
                                            <td><?=$restaurant['name_en']?></td>
                                            <td><?=$order['item']?></td>
                                            <td><?=$order['sub_total']?></td>
                                            <td><?=$order['sub_items']?></td>
                                            <td><?=$order['qty']?></td>

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