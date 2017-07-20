<?php
include "header.php";

 $company_id = $_GET['companies_id'];


?>
<div id="main" role="main">

    <!-- MAIN CONTENT -->
    <div id="content">
        <!-- row -->
        <div class="row">

            <!-- col -->

        </div>

        <?php DB::useDB(B2B_DB);
        $timings = DB::query("select * from delivery_timings where company_id = '$company_id'");

        ?>
        <!-- widget grid -->

        <section id="widget-grid"  id="myform">

            <!-- SHOW CATEGORIES-->
            <?php  if(!empty($timings)) { ?>
                <div class="row">
                    <!-- NEW WIDGET START -->
                    <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                        <div class="jarviswidget jarviswidget-color-darken" id="wid-id-0" data-widget-editbutton="false">

                            <header>
                                <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                                <h2>Timings</h2>
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


                                            <th data-hide="phone"><i class="fa-fw fa fa-info text-muted hidden-md hidden-sm hidden-xs"></i> Start Time </th>
                                            <th data-hide="phone"><i class="fa-fw fa fa-info text-muted hidden-md hidden-sm hidden-xs"></i> End Time </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php

                                        foreach($timings as $timing)
                                        {
                                            $time = explode("-",$timing['delivery_timing']);
                                            $start_time = $time[0];
                                            $end_time = $time[1];
                                            ?>
                                            <tr>


                                                <td><?=$start_time?></td>
                                                <td><?=$end_time?></td>

                                            </tr>
                                        <?php  } ?>

                                        </tbody>
                                    </table>
                                </div>

                            </div>

                        </div>
                    </article>

                </div>
                <!-- SHOW CATEGORIES END-->
            <?php  } ?>

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
                                <div onclick="show_delivery_div()" class="btn btn-primary btn-lg">
                                    <i class="fa fa-plus"></i>
                                    Add Delivery Time
                                </div>
                                <br><br>
                                <div id="add-delivery-time" style="display: none">
                                    <form>
                                        <fieldset>
                                            <input name="company_id" id="company_id" value="<?=$company_id?>" type="hidden">

                                            <div class="form-group">
                                                <label>Start Time</label>
                                                <input class="form-control" id="start_time" name="start_time" placeholder="Enter Start time" type="text">
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="start_time_error"></span>
                                            </div>
                                            <div class="form-group">
                                                <label>End Time</label>
                                                <input style="" class="form-control" id="end_time" placeholder="Enter End time" name="end_time"  type="text">
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="end_time_error"></span>
                                            </div>

                                            <br>
                                        </fieldset>
                                        <div class="form-actions">
                                            <div onclick="add_delivery_timing('<?=$_SERVER['REQUEST_URI']?>')" class="btn btn-primary btn-lg">
                                                <i class="fa fa-save"></i>
                                                Submit
                                            </div>
                                            <!--                                            <input type="submit" value="Submit" class="btn btn-primary btn-lg">-->
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
