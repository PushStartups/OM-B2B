<?php
include 'header.php';

$company_id = $_GET['company_id'];

DB::useDB(B2B_DB);
$company_detail = DB::queryFirstRow("select * from company where id = '$company_id'");
if($company_detail['company_delivery_option'] == '1') {

    $today = date('l');

    DB::useDB(B2B_DB);
    $get_delivery_time = DB::queryFirstRow("select * from company_timing where week_en = '$today' and  company_id = '$company_id'");


    date_default_timezone_set("Asia/Jerusalem");
    $current = date("H:i");


    $show_detail = 0;
    if ($current > $get_delivery_time['closing_timing']) {

        DB::query("select * from company_delivery_detail where date = CURDATE() AND company_id = '$company_id'");

        if (DB::count() == 0) {

            //TIME PASSED
            $show_detail = 1;

            DB::useDB(B2B_DB);
            $restaurant_id = DB::query("SELECT DISTINCT (restaurant_id) FROM  b2b_orders WHERE DATE( DATE ) = CURDATE() ");
            $total_restaurants = DB::count();


            $arr = [];
            $count = 0;
            foreach ($restaurant_id as $ids) {
                DB::useDB(B2B_RESTAURANTS);
                $rest_name = DB::queryFirstRow("select name_en from restaurants where id = '" . $ids['restaurant_id'] . "'");
                $arr[$count] = $rest_name['name_en'];
                $count++;
            }

            DB::useDB(B2B_DB);
            $delivery_charge = DB::queryFirstRow("select delivery_charge from company where id = '$company_id'");

            $rest_list = implode(',', $arr);

            DB::useDB(B2B_DB);
            DB::insert('company_delivery_detail', array(
                "company_id" => $company_id,
                "restaurants" => $rest_list,
                "delivery_charges" => $total_restaurants * $delivery_charge['delivery_charge'],
                "num_of_restaurants" => $total_restaurants,
                "date" => date("Y-m-d")

            ));
        }

    } else {
        //echo "time rehta hai";
        $show_detail = 0;
    }
}
?>
<div id="main" role="main">


    <!-- MAIN CONTENT -->
    <div id="content">

        <!-- row -->
        <div class="row">

            <!-- col -->
            <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-briefcase "></i>Delivery Detail</h1>
            </div>
            <!-- end col -->

            <!-- right side of the page with the sparkline graphs -->

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
            <?php if($company_detail['company_delivery_option'] == '1') { ?>
            <div class="row">
                <!-- NEW WIDGET START -->
                <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <!-- Widget ID (each widget will need unique ID)-->
                    <div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-1" data-widget-editbutton="false">

                        <header>
                            <span class="widget-icon"> <i class="fa fa-truck"></i> </span>
                            <h2>Delivery Details </h2>
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
                                        <th> Rstaurants</th>
                                        <th> Number Of Restaurants </th>
                                        <th >Delivery Charge</th>

                                        <th >Date</th>

                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php
                                    DB::useDB(B2B_DB);
                                    $delivery     =  DB::query("select * from company_delivery_detail");
                                    foreach ($delivery  as $del)
                                    {
                                        ?>
                                        <tr>
                                            <td><?=$del['restaurants']?></td>
                                            <td><?=$del['num_of_restaurants']?></td>
                                            <td><?=$del['delivery_charges']?></td>
                                            <td><?=$del['date']?></td>
                                        </tr>
                                        <?php
                                    } ?>
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
            <?php } else { ?>
            <div align="center" class="row">
                <h1 align="center"><b>Delivery Charges Not Applicable For this company</b></h1><br>
                <a align="center" href="companies.php" ><button class="btn-lg btn-labeled btn-primary" style="border-color: #4c4f53;">Go Back</button></a>
            </div>
            <?php } ?>
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


