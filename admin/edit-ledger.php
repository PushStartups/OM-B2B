<?php
include "header.php";

if(isset($_GET['id']))
{
    $ledger_id                 =    $_GET['id'];

}
else
{
    header("location:ledger.php");
}
?>
<div id="main" role="main">

    <!-- MAIN CONTENT -->
    <div id="content">
        <!-- row -->
        <div class="row">

            <!-- col -->
            <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-cutlery "></i> Update Ledger</h1>
            </div>

        </div>
        <div id="myform">
            <section id="widget-grid"  id="myform">
                <!-- row -->
                <div class="row">
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
                                    <?php
                                    DB::useDB(B2B_B2C_COMMON);
                                    $restaurant = DB::queryFirstRow("select * from b2b_ledger where id = '$ledger_id'")  ?>
                                    <form  method="post" enctype="multipart/form-data">

                                        <fieldset>
                                            <input name="authenticity_token" type="hidden">

                                            <div class="form-group">
                                                <label>Restaurnat </label>
                                                <select id="restaurant_id" name="restaurant_id" class="form-control">
                                                    <?php
                                                    DB::useDB('orderapp_restaurants');
                                                    $city = DB::query("select * from restaurants");
                                                    foreach($city as $cities)
                                                    {
                                                        if($restaurant['restaurant_id'] == $cities['id']){
                                                            ?>

                                                            <option value ="<?=$cities['id']?>" selected><?=$cities['name_en']?></option>
                                                        <?php  } else{ ?>
                                                            <option value ="<?=$cities['id']?>"><?=$cities['name_en']?></option>
                                                        <?php }
                                                    }
                                                    ?>
                                                </select>

                                            </div>

                                            <div class="form-group">
                                                <label>Payment Method</label>
                                                <select id="payment_method" name="payment_method" class="form-control">
                                                    <?php if($restaurant['payment_method'] == "CASH"){  ?>
                                                        <option value="CASH" selected>CASH</option>
                                                        <option value="Credit Card">Credit Card</option>
                                                    <?php } else { ?>
                                                        <option value="Credit Card" selected>Credit Card</option>
                                                        <option value="CASH">CASH</option>

                                                    <?php } ?>
                                                </select>
                                                <span style="font-size: 14px; color: red; width: 100%; padding: 9px;text-transform: none;"></span>
                                            </div>
                                            <div class="form-group">
                                                <label>Pickup Or Delivery</label>
                                                <select id="delivery_or_pickup" name="delivery_or_pickup" class="form-control">
                                                    <?php if($restaurant['delivery_or_pickup'] == "Delivery"){  ?>
                                                        <option value="Delivery" selected>Delivery</option>
                                                        <option value="Pick">Pick</option>
                                                    <?php } else { ?>
                                                        <option value="Pick" selected>Pick</option>
                                                        <option value="Delivery">Delivery</option>

                                                    <?php } ?>
                                                </select>
                                            </div>



                                            <div class="form-group">
                                                <label>Order No</label>
                                                <input class="form-control" id="order_no" name="order_no" value="<?=$restaurant['order_no']?>"  type="text">
                                            </div>
                                            

                                            <div class="form-group">
                                                <label>Restaurant Total</label>
                                                <input class="form-control" id="restaurant_total" name="restaurant_total" value="<?=$restaurant['restaurant_total']?>" type="text">

                                            </div>
                                            <div class="form-group">
                                                <label>Customer Grand Total</label>
                                                <input class="form-control" id="customer_grand_total" name="customer_grand_total" value="<?=$restaurant['customer_grand_total']?>" type="text">

                                            </div>

                                            <div class="form-group">
                                                <label>Customer Total Paid To Restaurant</label>
                                                <input class="form-control" id="customer_total_paid_to_restaurant" name="customer_total_paid_to_restaurant" value="<?=$restaurant['customer_total_paid_to_restaurant']?>" type="text">
                                            </div>

                                            <br>
                                        </fieldset>
                                        <div class="form-actions">
                                            <div onclick="update_ledger('<?=$ledger_id?>')" class="btn btn-primary btn-lg">
                                                <i class="fa fa-save"></i>
                                                Update
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!-- end widget content -->
                            </div>
                            <!-- end widget div -->
                        </div>

                    </article>
                    <!-- WIDGET END -->
                </div>
                <div class="row">
                    <!-- a blank row to get started -->
                    <div class="col-sm-12">
                        <!-- your contents here -->
                    </div>

                </div>
                <!-- end row -->
            </section>
        </div>
        <!-- end widget grid -->
    </div>
    <!-- END MAIN CONTENT -->
</div>



<div id="divBackground" style="position: fixed; z-index: 999; height: 100%; width: 100%;
        top: 0; left:0; background-color: Black; filter: alpha(opacity=60); opacity: 0.6; -moz-opacity: 0.8;display:none">
</div>
<!-- END MAIN PANEL -->
<?php
include "footer.php";
?>
