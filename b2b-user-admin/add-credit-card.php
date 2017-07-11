<?php
include "header.php";
    DB::useDB('orderapp_b2b');
    $cards   = DB::query("select * from user_credit_cards where user_id = '".$_SESSION['user_id']."'");


?>
<div id="main" role="main">

    <!-- MAIN CONTENT -->
    <div id="content">
        <section id="widget-grid"  id="myform">

            <!-- SHOW CATEGORIES-->
            <?php  if(!empty($cards)) { ?>
                <div class="row">
                    <!-- NEW WIDGET START -->
                    <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                        <div class="jarviswidget jarviswidget-color-darken" id="wid-id-0" data-widget-editbutton="false">

                            <header>
                                <span class="widget-icon"> <i class="fa fa-credit-card"></i> </span>
                                <h2>Credit Card Info</h2>
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
                                    <table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
                                        <thead>
                                        <tr>
                                            <th data-hide="phone">ID</th>
                                            <th data-hide="phone"> Credit Card Mask </th>
                                            <th data-hide="phone"> Expiration </th>
                                            <th data-hide="phone,tablet"> Delete</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php

                                        foreach( $cards as $card) {
                                            ?>
                                            <tr>
                                                <td><?=$card['id']?></td>
                                                <td><?=$card['card_mask']?></td>
                                                <td><?=$card['expiration']?></td>
                                                <td><a onclick="delete_card('<?=$card['id']?>','<?=$_SERVER['REQUEST_URI']?>')"><button class="btn btn-labeled btn-danger txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-trash-o"></i> Delete</button></a></td>
                                            </tr>
                                        <?php  }
                                        ?>

                                        </tbody>
                                    </table>
                                </div>

                            </div>

                        </div>
                    </article>

                </div>
                <!-- SHOW CATEGORIES END-->
            <?php  }  ?>

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

                                <div onclick="show_card_div()" class="btn btn-primary btn-lg">
                                    <i class="fa fa-plus"></i>
                                    Add Credit Card
                                </div>
                                <br><br>
                                <div id="add-card" style="display: none">
                                    <form>
                                        <fieldset>
                                            <input name="authenticity_token" type="hidden">

                                            <div class="form-group">
                                                <label>Credit Card No</label>
                                                <input class="form-control" id="card_no" name="card_no" placeholder="Enter Card Number" type="text">
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="card_error"></span>
                                            </div>
                                            <div class="form-group">
                                                <label>Expiry Month</label>
                                                <input class="form-control" placeholder="Enter Expiry Month"  id="expiry_month" name="expiry_month"  type="text">
                                                <span style="font-size: 14px; color: red; width: 100%; padding: 9px;text-transform: none;" id="expiry_month_error"></span>
                                            </div>
                                            <div class="form-group">
                                                <label>Expiry Year</label>
                                                <input class="form-control" placeholder="Enter Expiry Year"  id="expiry_year" name="expiry_year"  type="text">
                                                <span style="font-size: 14px; color: red; width: 100%; padding: 9px;text-transform: none;" id="expiry_year_error"></span>
                                            </div>

                                            <div class="form-group">
                                                <label>CVV</label>
                                                <input class="form-control" id="cvv" name="cvv" placeholder="Enter CVV Detail" type="text">
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="cvv_error"></span>
                                            </div>


                                        </fieldset>
                                        <div class="form-actions">
                                            <div onclick="add_card('<?=$_SESSION['user_id']?>','<?=$_SERVER['REQUEST_URI']?>')" class="btn btn-primary btn-lg">
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

</div>
<!-- END MAIN PANEL -->
<?php
include "footer.php";
?>
