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
                <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-briefcase "></i> Companies</h1>
            </div>
            <!-- end col -->

            <!-- right side of the page with the sparkline graphs -->
            <!-- col -->
            <div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">
                <!-- sparks -->
                <a style="float:right" href="add-new-company.php" class="btn btn-lg bg-color-purple txt-color-white"><i class="fa-fw fa fa-plus "></i> Add New Company</a>
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
                            <h2>Companies </h2>
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

                                        <th >Company Name</th>
                                        <th data-hide="phone, tablet">Delivery Address</th>
                                        <th data-hide="phone, tablet">Discount</th>
                                        <th data-hide="phone, tablet">Team Size</th>

                                        <th data-hide="phone, tablet">Contact Number</th>
                                        <th data-hide="phone, tablet">Contact Email</th>
                                        <th data-hide="phone, tablet">Ledger Link</th>
                                        <th data-hide="phone, tablet">Order DeadLine</th>
                                        <th data-hide="phone, tablet">Delivery Time</th>
                                        <th data-hide="phone, tablet">Add Or View Restaurants</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php
                                    $company = getAllCompanies();
                                    foreach ($company as $companies)
                                    {

                                        date_default_timezone_set("Asia/Jerusalem");
                                        $day = date('l');
                                        DB::useDB(B2B_DB);
                                        $getDay = DB::queryFirstRow("select * from company_timing where week_en = '$day' and company_id = '".$companies['id']."' ");
                                        $ordering_deadline_time = $getDay['closing_time'];
                                        ?>
                                        <tr>

                                            <td><?=$companies['name']?></td>
                                            <td><?=$companies['delivery_address']?></td>
                                            <td><?=$companies['discount_type']?> (<?=$companies['discount']?>)</td>

                                            <td><?=$companies['team_size']?></td>

                                            <td><?=$companies['contact_number']?></td>
                                            <td><?=$companies['contact_email']?></td>
                                            <td><a href="<?=$companies['ledger_link']?>" target="_blank"><?=$companies['ledger_link']?></a> </td>
                                            <td><?=$ordering_deadline_time; ?> </td>
                                            <?php if(($ordering_deadline_time != "Closed") || ($ordering_deadline_time != "")){  ?>
                                            <td><?=getDeliveryTime($ordering_deadline_time); ?> </td>
                                            <?php }  else { ?>
                                                <td>Closed</td>
                                            <?php } ?>
                                            <td><a href="add-company-restaurant.php?companies_id=<?=$companies['id']?>"><button class="btn btn-labeled btn-primary  txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-plus"></i>Default Restaurants </button></a></td>
                                            <td><a href="edit-company.php?id=<?=$companies['id']?>"><button class="btn btn-labeled btn-primary bg-color-blueDark txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-info"></i> Detail </button></a></td>
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
<?php
function getDeliveryTime($orig_date){
    if(($orig_date == "") || ($orig_date == null) ||($orig_date == "Closed"))
    {
        return "Closed";
    }
    $seconds = strtotime($orig_date);

    $plus_one_hour = $seconds + 3600;

    $next_hour = floor($plus_one_hour / 3600) * 3600;

    $mydate =  date("H:i",$next_hour);

    $exact_time = explode(':',$mydate);

    $old_time = explode(':',$orig_date);


    return $final_time = $exact_time[0].':'.$old_time[1];
}
?>
?>
<!-- END MAIN PANEL -->
<?php
include "footer.php";
?>
