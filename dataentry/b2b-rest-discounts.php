<?php
include "header.php";
?>
    <!-- MAIN PANEL -->
    <div id="main" role="main">


        <!-- MAIN CONTENT -->
        <div id="content">

            <!-- row -->
            <div class="row">

                <!-- col -->
                <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                    <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-tags "></i>B2B Restaurant Discounts</h1>
                </div>
                <!-- end col -->

                <!-- right side of the page with the sparkline graphs -->
                <!-- col -->
                <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                    <a style="text-decoration: none" href="add-rest-company-discount.php"><div class="btn btn-purple btn-lg"><i class="fa-fw fa fa-plus "></i> Add A Company Restaurant Discount</div></a>
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
                                <span class="widget-icon"> <i class="fa fa-tags"></i> </span>
                                <h2>B2B Restaurant Discounts </h2>
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
                                            <th data-class="expand">Company</th>

                                            <th >Restaurant</th>

                                            <th data-hide="phone, tablet">Discount</th>


                                            <th>Action</th>
                                            <th>Delete</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?php
                                        //  DB::query("select brd.*,c.name,r.name_en from b2b_rest_discounts as brd inner join restaurants as r on brd.rest_id = r.id  inner join company as c on brd.company_id = c.id");
                                        DB::useDB('orderapp_b2b');
                                        $b2bRestDiscounts = DB::query("select * from b2b_rest_discounts");
                                        //$b2bRestDiscounts = getAllB2BRestDiscounts();
                                        foreach ($b2bRestDiscounts as $companies)
                                        {
                                            DB::useDB('orderapp_b2b');
                                            $company = DB::queryFirstRow("select * from company     where id = '".$companies['company_id']."'");
                                            DB::useDB('orderapp_restaurants');
                                            $restaurant = DB::queryFirstRow("select * from restaurants where id = '".$companies['rest_id']."'");


                                            ?>
                                            <tr>
                                                <td><?=$company['name']?></td>

                                                <td><?=$restaurant['name_en']?></td>

                                                <td><?=$companies['discount_percent']?></td>

                                              <td><a href="edit-b2b-rest-discount.php?id=<?=$companies['id']?>"><button class="btn btn-labeled btn-primary bg-color-blueDark txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-edit"></i> Edit </button></a></td>
                                                <td><a onclick="delete_b2b_rest_disc('<?=$companies['id']?>','<?=$_SERVER['REQUEST_URI']?>')"><button class="btn btn-labeled btn-danger txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-trash-o"></i> Delete</button></a></td>
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
    <!-- END MAIN PANEL -->
<?php
include "footer.php";
?>