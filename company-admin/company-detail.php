<?php
include "header.php";

$company_id = $_SESSION['company_id'];

$company               =    getSpecificCompanies($company_id);


date_default_timezone_set("Asia/Jerusalem");
$day = date('l');
DB::useDB('orderapp_b2b_wui');

$getDay = DB::queryFirstRow("select * from company_timing where week_en = '$day' and company_id = '$company_id' ");
$ordering_deadline_time = $getDay['closing_time'];

$delivery_time = $getDay['delivery_timing'];

?>


<div id="main" role="main">

    <div id="content">
        <!-- row -->
        <div class="row">

            <!-- col -->
            <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-briefcase "></i> Edit Company <?php echo $company['name'];?></h1>
            </div>

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

                    <div class="jarviswidget" id="wid-id-2" data-widget-colorbutton="false" data-widget-editbutton="false">

                        <header>
                        </header>

                        <div>

                            <div class="jarviswidget-editbox">
                                <!-- This area used as dropdown edit box -->
                            </div>

                            <div class="widget-body">
                                <style>
                                    .map_canvas {
                                        width: 500px;
                                        height: 300px;
                                        margin: 10px 20px 10px 0;
                                    }

                                </style>
                                <form>

                                    <fieldset>
                                        <input name="authenticity_token" type="hidden">





                                        <div style="display:none" class="form-group">
                                            <div id="map" class="map_canvas"></div>
                                        </div>



                                        <div class="form-group">
                                            <label>Contact Name </label>
                                            <input class="form-control" id="contact_name" name="contact_name" value="<?=$company['contact_name'];?>" placeholder="Enter Contact Name" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_contact_name"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Contact Phone Number </label>
                                            <input class="form-control" id="contact_number" name="contact_number" value="<?=$company['contact_number'];?>" placeholder="Enter Contact Name" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_contact_number"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Contact Email </label>
                                            <input class="form-control" id="contact_email" name="contact_email" value="<?=$company['contact_email'];?>" placeholder="Enter Contact Email" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_contact_email"></span>
                                        </div>


                                        <div class="form-group">
                                            <label>Registered Company Number</label>
                                            <input class="form-control" id="registered_company_number" name="registered_company_number" placeholder="Enter Company Number" value="<?php echo $company['registered_company_number'];?>" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_registered_company_number"></span>
                                        </div>



                                        <div class="form-group">
                                            <label>Company Email</label>
                                            <input class="form-control" id="compnay_email" name="compnay_email" placeholder="Enter Email" type="text" value="<?=$company['email']?>">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_compnay_email"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Company Password</label>
                                            <input class="form-control" id="company_password" name="company_password" placeholder="Enter Password" type="text" value="<?=$company['password']?>">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_company_password"></span>
                                        </div>


                                        <div class="form-group">
                                            <label>Company Deadline Time</label>
                                            <input class="form-control" id="company_deadline_time" name="company_deadline_time" placeholder="Enter Deadline Time" type="text" value="<?=$ordering_deadline_time?>">
                                            <input class="form-control" id="week_en" name="week_en" type="hidden" value="<?=$getDay['week_en'];?>">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_company_deadline_time"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Delivery Time</label>
                                            <input class="form-control" id="delivery_time" name="delivery_time" type="text"  value="<?=$delivery_time;?>">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_delivery_time"></span>
                                        </div>





                                    </fieldset>
                                    <div class="form-actions">
                                        <div onclick="edit_company('<?=$company_id?>','<?=$_SERVER['REQUEST_URI']?>')" class="btn btn-primary btn-lg">
                                            <i class="fa fa-save"></i>
                                            Submit
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
        <!-- end widget grid -->
    </div>




    <!-- END MAIN PANEL -->
    <?php
    include "footer.php";
    ?>
