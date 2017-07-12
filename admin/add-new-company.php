<?php
include "header.php";
?>
<div id="main" role="main">

    <!-- MAIN CONTENT -->
    <div id="content">
        <!-- row -->
        <div class="row">

            <!-- col -->
            <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-briefcase "></i> Add A Company</h1>
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

                                <form>
                                    <fieldset>
                                        <style>
                                            .map_canvas {
                                                width: 500px;
                                                height: 300px;
                                                margin: 10px 20px 10px 0;
                                            }

                                        </style>
                                        <input name="authenticity_token" type="hidden">
                                        <div class="form-group">
                                            <label>Company Name</label>
                                            <input class="form-control" id="name" name="name" placeholder="Enter Company Name" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_name"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Registered Company Number</label>
                                            <input class="form-control" id="registered_company_number" name="registered_company_number" placeholder="Enter Company Number" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_registered_company_number"></span>
                                        </div>

                                        <div style="display:none" class="form-group">
                                            <div id="map" class="map_canvas"></div>
                                        </div>
                                        <input type="hidden" id="lat" name="lat">
                                        <input type="hidden" id="lng" name="lng">
                                        <div class="form-group">
                                            <label>Delivery Address</label>
                                            <input class="form-control" id="area_en" name="area_en"  placeholder="Enter Delivery Address" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_area_en"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Minimum Order</label>
                                            <input class="form-control" id="min_order" name="min_order" placeholder="Enter Minimum Order" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_min_order"></span>
                                        </div>
                                        <div class="form-group">
                                            <label>Discount Type </label>
                                            <select id="discount_type" name="discount_type" class="form-control">
                                                <option>daily</option>
                                                <option>monthly</option>
                                            </select>
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Discount Amount </label>
                                            <input class="form-control" id="amount" name="amount" placeholder="Enter Discount Amount" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_amount"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Team Size </label>
                                            <input class="form-control" id="team_size" name="team_size" placeholder="Enter Team Size" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_team_size"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Restaurant Limit </label>
                                            <input class="form-control" id="limit_of_restaurants" name="limit_of_restaurants" placeholder="Enter Limit Of Restaurant" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_limit_of_restaurants"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Ordering Deadline Time </label>
                                            <input class="form-control" id="ordering_deadline_time" name="ordering_deadline_time" placeholder="Ordering Deadline" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_ordering_deadline_time"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Delivery Time </label>
                                            <input class="form-control" id="delivery_time" name="delivery_time" placeholder="Delivery Time" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_delivery_time"></span>
                                        </div>


                                        <div class="form-group">
                                            <label>Contact Name </label>
                                            <input class="form-control" id="contact_name" name="contact_name" placeholder="Enter Contact Name" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_contact_name"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Contact Phone Number </label>
                                            <input class="form-control" id="contact_number" name="contact_number" placeholder="Enter Contact Name" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_contact_number"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Contact Email </label>
                                            <input class="form-control" id="contact_email" name="contact_email" placeholder="Enter Contact Email" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_contact_email"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Ledger Link </label>
                                            <input class="form-control" id="ledger_link" name="ledger_link" placeholder="Enter Ledger Link" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_ledger_link"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Company Email</label>
                                            <input class="form-control" id="email" name="email" placeholder="Enter Email" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_email"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Company Password</label>
                                            <input class="form-control" id="password" name="password" placeholder="Enter Password" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_password"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Notes</label>
                                            <textarea class="form-control" id="notes" name="notes" placeholder="Enter notes"></textarea>
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_notes"></span>
                                        </div>

                                        <br>
                                        <h3 align="center">Company Timings</h3><br>
                                        <h6>*Please write "Closed" on textfield in case of close start and end timings.</h6>
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
                                                        <input type="text" id="sunday_start_time" class="form-control" placeholder="Select Time">
                                                            <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                            </span>

                                                    </div>
                                                    <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_sunday_start_time"></span>
                                                </td>
                                                <td>
                                                    <div class="input-group form-group clockpicker">
                                                        <input type="text" id="sunday_end_time" class="form-control" placeholder="Select Time">
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
                                                        <input type="text" id="monday_start_time" class="form-control" placeholder="Select Time">
                                                            <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                            </span>
                                                    </div>
                                                    <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_monday_start_time"></span>

                                                </td>
                                                <td>
                                                    <div class="input-group form-group clockpicker">
                                                        <input type="text" id="monday_end_time" class="form-control" placeholder="Select Time">
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
                                                        <input type="text" id="tuesday_start_time" class="form-control" placeholder="Select Time">
                                                            <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                            </span>
                                                    </div>
                                                    <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_tuesday_start_time"></span>
                                                </td>
                                                <td>
                                                    <div class="input-group form-group clockpicker">
                                                        <input type="text" id="tuesday_end_time" class="form-control" placeholder="Select Time">
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
                                                        <input type="text" id="wednesday_start_time" class="form-control" placeholder="Select Time">
                                                            <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                            </span>
                                                    </div>
                                                    <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_wednesday_start_time"></span>

                                                </td>
                                                <td>
                                                    <div class="input-group form-group clockpicker">
                                                        <input type="text" id="wednesday_end_time" class="form-control" placeholder="Select Time">
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
                                                        <input type="text" id="thursday_start_time" class="form-control" placeholder="Select Time">
                                                            <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                            </span>
                                                    </div>
                                                    <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_thursday_start_time"></span>
                                                </td>
                                                <td>
                                                    <div class="input-group form-group clockpicker">
                                                        <input type="text" id="thursday_end_time" class="form-control" placeholder="Select Time">
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
                                                        <input type="text" id="friday_start_time" class="form-control" placeholder="Select Time">
                                                            <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                            </span>
                                                    </div>
                                                    <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_friday_start_time"></span>
                                                </td>
                                                <td>
                                                    <div class="input-group form-group clockpicker">
                                                        <input type="text" id="friday_end_time" class="form-control" placeholder="Select Time">
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
                                                        <input type="text" id="saturday_start_time" class="form-control" placeholder="Select Time">
                                                            <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                            </span>
                                                    </div>
                                                    <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_saturday_start_time"></span>
                                                </td>
                                                <td>
                                                    <div class="input-group form-group clockpicker">
                                                        <input type="text" id="saturday_end_time" class="form-control" placeholder="Select Time">
                                                        <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>
                                                    </div>
                                                    <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_saturday_end_time"></span>
                                                </td>
                                            </tr>

                                            </tbody>

                                        </table>



                                    </fieldset>
                                    <div class="form-actions">
                                        <div onclick="add_company()" class="btn btn-primary btn-lg">
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
<!-- END MAIN PANEL -->
<?php
include "footer.php";
?>
