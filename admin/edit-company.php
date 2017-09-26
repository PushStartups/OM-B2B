<?php
include "header.php";
?>
<div id="main" role="main">
    <?php

    if(isset($_GET['id']))
    {
        $companies_id = $_GET['id'];
        $edit_company = getSpecificCompanies($companies_id);


        date_default_timezone_set("Asia/Jerusalem");
        $day = date('l');
        DB::useDB(B2B_DB);

        $getDay = DB::queryFirstRow("select * from company_timing where week_en = '$day' and company_id = '$companies_id' ");

        $delivery_time = $getDay['delivery_timing'];


        $timings = getSpecificCompanyTiming($companies_id);

        $count = 1;
        $week1[] ="";
        $week2[] ="";
        $week3[] ="";
        $week4[] ="";
        $week5[] ="";
        $week6[] ="";
        $week7[] ="";

        foreach ($timings as $time)
        {
            if($count == 1)
            {
                $week1['id']                    =  $time['id'];
                $week1['opening_time']          =   $time['opening_time'];
                $week1['closing_time']          =   $time['closing_time'];
            }
            if($count == 2)
            {
                $week2['id']                    =  $time['id'];
                $week2['opening_time']          =   $time['opening_time'];
                $week2['closing_time']          =   $time['closing_time'];
            }
            if($count == 3)
            {
                $week3['id']                    =  $time['id'];
                $week3['opening_time']          =   $time['opening_time'];
                $week3['closing_time']          =   $time['closing_time'];
            }
            if($count == 4)
            {
                $week4['id']                    =  $time['id'];
                $week4['opening_time']          =   $time['opening_time'];
                $week4['closing_time']          =   $time['closing_time'];
            }
            if($count == 5)
            {
                $week5['id']                    =  $time['id'];
                $week5['opening_time']          =   $time['opening_time'];
                $week5['closing_time']          =   $time['closing_time'];
            }
            if($count == 6)
            {
                $week6['id']                    =  $time['id'];
                $week6['opening_time']          =   $time['opening_time'];
                $week6['closing_time']          =   $time['closing_time'];
            }
            if($count == 7)
            {
                $week7['id']                    =  $time['id'];
                $week7['opening_time']          =   $time['opening_time'];
                $week7['closing_time']          =   $time['closing_time'];
            }
            $count++;

        }
    }
    ?>
    <!-- MAIN CONTENT -->
    <div id="content">
        <!-- row -->
        <div class="row">

            <!-- col -->
            <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-briefcase "></i> Edit Company <?php echo $edit_company['name'];?></h1>
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
                                    <div class="form-actions">
                                        <div onclick="delete_company('<?=$companies_id?>')" class="btn btn-danger btn-lg">
                                            <i class="fa fa-save"></i>
                                            Delete Company
                                        </div>
                                    </div>
                                    <fieldset>
                                        <input name="authenticity_token" type="hidden">
                                        <div class="form-group">
                                            <label>Company Name</label>
                                            <input class="form-control" id="name" name="name" value="<?php echo $edit_company['name'];?>"  type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_name"></span>
                                        </div>


                                        <div class="form-group">
                                            <label>Registered Company Number</label>
                                            <input class="form-control" id="registered_company_number" name="registered_company_number" placeholder="Enter Company Number" value="<?php echo $edit_company['registered_company_number'];?>" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_registered_company_number"></span>
                                        </div>


                                        <div style="display:none" class="form-group">
                                            <div id="map" class="map_canvas"></div>
                                        </div>
                                        <input type="hidden" id="lat" name="lat" value="<?=$edit_company['lat'];?>">
                                        <input type="hidden" id="lng" name="lng" value="<?=$edit_company['lng'];?>">
                                        <div class="form-group">
                                            <label>Delivery Address</label>
                                            <input class="form-control" id="area_en" name="area_en" value="<?=$edit_company['delivery_address'];?>"  type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_address"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Minimum Order</label>
                                            <input class="form-control" id="min_order" name="min_order" value="<?=$edit_company['min_order'];?>"  type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_min_order"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Discount Type </label>
                                            <select id="discount_type" name="discount_type" class="form-control">
                                                <?php if($edit_company['discount_type'] == "daily"){ ?>
                                                    <option value="daily" selected>daily</option>
                                                    <option value="monthly">monthly</option>
                                                <?php  } else { ?>
                                                    <option value="daily">daily</option>
                                                    <option value="monthly" selected>monthly</option>
                                                <?php } ?>
                                            </select>
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;"></span>
                                        </div>

                                        <div class="form-group">
                                            <p><b>*If you change the discount amount, it will affect on all the users associated with this company</b></p>
                                            <label>Discount Amount </label>
                                            <input class="form-control" id="amount" name="amount" value="<?=$edit_company['discount'];?>" placeholder="Enter Discount Amount" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_amount"></span>
                                        </div>



                                        <div class="form-group">
                                            <label>Team Size </label>
                                            <input class="form-control" id="team_size" name="team_size" placeholder="Enter Team Size" value="<?=$edit_company['team_size'];?>" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_team_size"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Restaurant Limit </label>
                                            <input class="form-control" id="limit_of_restaurants" name="limit_of_restaurants" value="<?=$edit_company['limit_of_restaurants'];?>" placeholder="Enter Limit Of Restaurant" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_limit_of_restaurants"></span>
                                        </div>


                                        <div class="form-group">
                                            <label>Contact Name </label>
                                            <input class="form-control" id="contact_name" name="contact_name" value="<?=$edit_company['contact_name'];?>" placeholder="Enter Contact Name" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_contact_name"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Contact Phone Number </label>
                                            <input class="form-control" id="contact_number" name="contact_number" value="<?=$edit_company['contact_number'];?>" placeholder="Enter Contact Name" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_contact_number"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Contact Email </label>
                                            <input class="form-control" id="contact_email" name="contact_email" value="<?=$edit_company['contact_email'];?>" placeholder="Enter Contact Email" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_contact_email"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Ledger Link </label>
                                            <input class="form-control" id="ledger_link" name="ledger_link" value="<?=$edit_company['ledger_link'];?>" placeholder="Enter Ledger Link" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_ledger_link"></span>
                                        </div>



                                        <div class="form-group">
                                            <label>Company Email</label>
                                            <input class="form-control" id="email" name="email" placeholder="Enter Email" type="text" value="<?=$edit_company['email']?>">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_email"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Compnay Password</label>
                                            <input class="form-control" id="password" name="password" placeholder="Enter Password" type="text" value="<?=$edit_company['password']?>">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_password"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Company Delivery Option *(Enable Delivery Charges For Company) </label>
                                            <select onchange="company_delivery_optionn_edit(this.value);" id="company_delivery_option" name="company_delivery_option" class="form-control">
                                                <?php if($edit_company['company_delivery_option'] == "0"){ ?>
                                                    <option value="0" selected>No</option>
                                                    <option value="1">Yes</option>
                                                <?php  } else { ?>
                                                    <option value="1" selected>Yes</option>
                                                    <option value="0" >No</option>
                                                <?php } ?>

                                            </select>
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;"></span>
                                        </div>

                                        <div <?php if($edit_company['company_delivery_option'] == "0"){ ?> style="display:none;" <?php } ?> id="delivery_charge" class="form-group">
                                            <label>Delivery Charges</label>
                                            <input class="form-control" id="d_charges" name="d_charges" value="<?=$edit_company['delivery_charge']?>" placeholder="Enter Delivery Charges" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_delivery_charges"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Notes</label>
                                            <textarea class="form-control" id="notes" name="notes" placeholder="Enter notes" ><?php echo $edit_company['notes']?></textarea>
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_notes"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Delivery Time </label>
                                            <input class="form-control" id="delivery_time" name="delivery_time" type="text"  value="<?=$delivery_time;?>">
                                            <input class="form-control" id="week_en" name="week_en" type="hidden" value="<?=$getDay['week_en'];?>">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_delivery_time"></span>
                                        </div>



                                        <br>
                                        <div class="row">
                                            <!-- NEW WIDGET START -->
                                            <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                                                <div class="jarviswidget jarviswidget-color-darken" id="wid-id-0" data-widget-editbutton="false">

                                                    <header>
                                                        <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                                                        <h2>Edit Company Timings</h2>
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

                                                            <form method="post">

                                                                <table id="" class="table table-striped table-bordered" width="100%">

                                                                    <thead>

                                                                    <tr>
                                                                        <th data-class="expand">Day</th>

                                                                        <th >Start Time</th>

                                                                        <th>Close Time</th>

                                                                    </tr>
                                                                    </thead>

                                                                    <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            Sunday
                                                                        </td>
                                                                        <td>
                                                                            <div class="input-group form-group clockpicker">
                                                                                <input type="text" id="sunday_start_time" class="form-control" placeholder="Select Time"
                                                                                       value="<?php echo $week7['opening_time'];?>">
                                                                                <input type="hidden" value="<?php echo $week1['id']; ?>" id="week1_id"/>
                                                                                <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                            </span>

                                                                            </div>
                                                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_sunday_start_time"></span>
                                                                        </td>
                                                                        <td>
                                                                            <div class="input-group form-group clockpicker">
                                                                                <input type="text" id="sunday_end_time" class="form-control" placeholder="Select Time"
                                                                                       value="<?php echo $week7['closing_time'];?>">
                                                                                <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>

                                                                            </div>
                                                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_sunday_end_time"></span>

                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td>
                                                                            Monday
                                                                        </td>
                                                                        <td>
                                                                            <div class="input-group form-group clockpicker">
                                                                                <input type="text" id="monday_start_time" class="form-control" placeholder="Select Time"
                                                                                       value="<?php echo $week1['opening_time'];?>">
                                                                                <input type="hidden" value="<?php echo $week2['id']; ?>" id="week2_id"/>
                                                                                <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                            </span>
                                                                            </div>
                                                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_monday_start_time"></span>

                                                                        </td>
                                                                        <td>
                                                                            <div class="input-group form-group clockpicker">
                                                                                <input type="text" id="monday_end_time" class="form-control" placeholder="Select Time"
                                                                                       value="<?php echo $week1['closing_time'];?>">
                                                                                <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>
                                                                            </div>
                                                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_monday_end_time"></span>
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td>
                                                                            Tuesday
                                                                        </td>
                                                                        <td>

                                                                            <div class="input-group form-group clockpicker">
                                                                                <input type="text" id="tuesday_start_time" class="form-control" placeholder="Select Time"
                                                                                       value="<?php echo $week2['opening_time'];?>">
                                                                                <input type="hidden" value="<?php echo $week3['id']; ?>" id="week3_id"/>
                                                                                <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                            </span>
                                                                            </div>
                                                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_tuesday_start_time"></span>
                                                                        </td>
                                                                        <td>
                                                                            <div class="input-group form-group clockpicker">
                                                                                <input type="text" id="tuesday_end_time" class="form-control" placeholder="Select Time"
                                                                                       value="<?php echo $week2['closing_time'];?>">
                                                                                <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>
                                                                            </div>
                                                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_tuesday_end_time"></span>
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td>
                                                                            Wednesday
                                                                        </td>
                                                                        <td>
                                                                            <div class="input-group form-group clockpicker">
                                                                                <input type="text" id="wednesday_start_time" class="form-control" placeholder="Select Time"
                                                                                       value="<?php echo $week3['opening_time'];?>">
                                                                                <input type="hidden" value="<?php echo $week4['id']; ?>" id="week4_id"/>
                                                                                <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                            </span>
                                                                            </div>
                                                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_wednesday_start_time"></span>

                                                                        </td>
                                                                        <td>
                                                                            <div class="input-group form-group clockpicker">
                                                                                <input type="text" id="wednesday_end_time" class="form-control" placeholder="Select Time"
                                                                                       value="<?php echo $week3['closing_time'];?>">
                                                                                <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>
                                                                            </div>
                                                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_wednesday_end_time"></span>
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td>
                                                                            Thursday
                                                                        </td>
                                                                        <td>
                                                                            <div class="input-group form-group clockpicker">
                                                                                <input type="text" id="thursday_start_time" class="form-control" placeholder="Select Time"
                                                                                       value="<?php echo $week4['opening_time'];?>">
                                                                                <input type="hidden" value="<?php echo $week5['id']; ?>" id="week5_id"/>
                                                                                <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                            </span>
                                                                            </div>
                                                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_thursday_start_time"></span>
                                                                        </td>
                                                                        <td>
                                                                            <div class="input-group form-group clockpicker">
                                                                                <input type="text" id="thursday_end_time" class="form-control" placeholder="Select Time"
                                                                                       value="<?php echo $week4['closing_time'];?>">
                                                                                <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>
                                                                            </div>
                                                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_thursday_end_time"></span>
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td>
                                                                            Friday
                                                                        </td>
                                                                        <td>
                                                                            <div class="input-group form-group clockpicker">
                                                                                <input type="text" id="friday_start_time" class="form-control" placeholder="Select Time"
                                                                                       value="<?php echo $week5['opening_time'];?>">
                                                                                <input type="hidden" value="<?php echo $week6['id']; ?>" id="week6_id"/>
                                                                                <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                            </span>
                                                                            </div>
                                                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_friday_start_time"></span>
                                                                        </td>
                                                                        <td>
                                                                            <div class="input-group form-group clockpicker">
                                                                                <input type="text" id="friday_end_time" class="form-control" placeholder="Select Time"
                                                                                       value="<?php echo $week5['closing_time'];?>">
                                                                                <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>
                                                                            </div>
                                                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_friday_end_time"></span>

                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td>
                                                                            Saturday
                                                                        </td>
                                                                        <td>
                                                                            <div class="input-group form-group clockpicker">
                                                                                <input type="text" id="saturday_start_time" class="form-control" placeholder="Select Time"
                                                                                       value="<?php echo $week6['opening_time'];?>">
                                                                                <input type="hidden" value="<?php echo $week7['id']; ?>" id="week7_id"/>
                                                                                <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                            </span>
                                                                            </div>
                                                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_saturday_start_time"></span>
                                                                        </td>
                                                                        <td>
                                                                            <div class="input-group form-group clockpicker">
                                                                                <input type="text" id="saturday_end_time" class="form-control" placeholder="Select Time"
                                                                                       value="<?php echo $week6['closing_time'];?>">
                                                                                <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>
                                                                            </div>
                                                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_saturday_end_time"></span>
                                                                        </td>
                                                                    </tr>

                                                                    </tbody>

                                                                </table>





                                                            </form>
                                                            <!--   RESTAURANT DELIVERY ADDRESS AND FEES-->

                                                        </div>

                                                    </div>

                                                </div>
                                            </article>

                                        </div>



                                    </fieldset>
                                    <div class="form-actions">
                                        <div onclick="edit_company('<?=$companies_id?>','<?=$_SERVER['REQUEST_URI']?>')" class="btn btn-primary btn-lg">
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
    <!-- END MAIN CONTENT -->
</div>
<script>
    function company_delivery_optionn_edit(val)
    {
        //alert(val);
        if(val == 1)
        {
            $("#delivery_charge").show();
            $("#d_charges").attr("required", "true");
        }
        else
        {
            $("#delivery_charge").hide();
            $("#d_charges").attr("required", "false");
        }

    }
</script>
<!-- END MAIN PANEL -->
<?php
include "footer.php";
?>
