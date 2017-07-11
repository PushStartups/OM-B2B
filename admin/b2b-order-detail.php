<?php
include "header.php";
?>
<!-- MAIN PANEL -->
<?php
if(isset($_GET['order_id'])){
    $order_id = $_GET['order_id'];
}
else{
    header("location:b2b-orders.php");
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
                    <li class="sparks-info">
                        <?php $info = getPaymentMethodB2B($order_id);
                        $transaction_id = $info['transaction_id'];
                        ?>
                        <?php $count = $info['total'];  ?>
                        <h5> <b>Payable Amount</b> <span class="txt-color-blue"><?=$count?></span></h5>
                    </li>

                    <li class="sparks-info">
                        <h5><b>Billing Amount</b><span class="txt-color-purple">&nbsp;<?=$info['billing_amount']?></span></h5>
                    </li>
                    <li class="sparks-info">
                        <h5><b>Remaining Balance</b><span class="txt-color-purple">&nbsp;<?=$info['remaining_balance']?></span></h5>
                    </li>

                    <li class="sparks-info">
                            <?php $refundAmount   =  getTotalRefundAmountB2B($order_id);  ?>
                            <h5><b>Refund Amount</b><span class="txt-color-purple">&nbsp;<?=$refundAmount?></span></h5>
                    </li>

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

        <!--   REFUND AREA-->
            <section id="widget-grid" class="">
                <!-- row -->
                <div class="row">
                    <!-- NEW WIDGET START -->
                    <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <!-- Widget ID (each widget will need unique ID)-->
                        <div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-1" data-widget-editbutton="false">

                            <header>
                                <span class="widget-icon"> <i class="fa fa-exchange"></i> </span>
                                <h2>Refund </h2>
                            </header><br>
                            <style>
                                .label {
                                    display: inline;
                                    padding: .2em .6em .3em;
                                    font-size: 100%;
                                    font-weight: 700;
                                    line-height: 1;
                                    color: #000;
                                    text-align: center;
                                    white-space: nowrap;
                                    vertical-align: baseline;
                                    border-radius: .25em;
                                }
                            </style>
                            <fieldset>

                                <section>
                                    <label class="label">Refund Amount</label>
                                    <label class="input">
                                        <input type="text" id="refund" name="refund" class="input-sm">
                                        <span style="color:red" class="refund_message" id="refund_message"></span>
                                    </label>

                                </section>
                            </fieldset>
                            <footer>
                                <button onclick="refend_amount('<?=$count?>','<?=$order_id?>','<?=$_SERVER['REQUEST_URI']?>','<?=$transaction_id?>')"  class="btn btn-primary">
                                    Submit
                                </button>

                            </footer>
                        </div>
                    </article>
                </div>
            </section>
            <?php  $counter = getRefundCount($order_id);
            if($counter > 0){
                ?>
                <!--   REFUND TABLE    -->
                <section id="widget-grid" class="">

                    <div class="row">
                        <!-- NEW WIDGET START -->
                        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                            <div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-1" data-widget-editbutton="false">

                                <header>
                                    <span class="widget-icon"> <i class="fa fa-exchange"></i> </span>
                                    <h2>Refund Detail </h2>

                                </header>

                                <div>
                                    <!-- widget edit box -->
                                    <div class="jarviswidget-editbox">

                                    </div>

                                    <div class="widget-body no-padding">

                                        <table id="datatable_fixed_column" class="table table-striped table-bordered" width="100%">

                                            <thead>

                                            <tr>
                                                <th data-class="expand">Order ID</th>
                                                <th >Refund Amount</th>


                                            </tr>
                                            </thead>

                                            <tbody>
                                            <?php $orders = getRefundDetail($order_id);
                                            foreach ($orders as $order)
                                            {
                                                ?>
                                                <tr>
                                                    <td><?=$order['order_id']?></td>
                                                    <td><?=$order['amount']?></td>

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
                    </div>

                    <div class="row">
                        <div class="col-sm-12">

                        </div>
                    </div>

                </section> <?php } ?>
            <!--   REFUND TABLE    -->
      

    </div>
    <!-- END MAIN CONTENT -->

</div>
<!-- END MAIN PANEL -->
<?php
include "footer.php";
?>