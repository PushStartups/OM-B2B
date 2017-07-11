<?php
include "header.php";
?>
    <!-- MAIN PANEL -->
    <div id="main" role="main">


        <!-- MAIN CONTENT -->
        <div id="content">

            <!-- row -->
            <div class="row">


                <!-- end col -->

                <!-- right side of the page with the sparkline graphs -->
                <!-- col -->


                <?php
                DB::useDB('orderapp_b2b');
                $companies = DB::queryFirstRow("select * from company where id = '".$_SESSION['company_id']."'");
                ?>

                <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                    <h2>Voting ON/OFF</h2>
                    <div class="onoffswitch">
                        <input type="checkbox" name="onoffswitchcompany" class="onoffswitch-checkbox" id="<?=$companies['id']?>" <?php if($companies['voting'] == '1'){ ?> checked <?php } ?>>
                        <label class="onoffswitch-label" for="<?=$companies['id']?>">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>
                <!-- end col -->

                <!-- right side of the page with the sparkline graphs -->
                <!-- col -->
                <div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">
                    <!-- sparks -->
                    <a style="float:right" href="add-vote-timing.php" class="btn btn-lg bg-color-purple txt-color-white"><i class="fa-fw fa fa-plus "></i> Add A Vote Timing</a>
                    <!-- end sparks -->
                </div>
                <!-- end col -->

            </div>

           <br><br>
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
                                            <th data-class="expand">ID</th>
                                            <th >Voting Start</th>
                                            <th data-hide="phone, tablet">Voting End</th>

<!--                                            <th>Action</th>-->
<!--                                            <th>Delete</th>-->
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?php
                                        //  DB::query("select brd.*,c.name,r.name_en from b2b_rest_discounts as brd inner join restaurants as r on brd.rest_id = r.id  inner join company as c on brd.company_id = c.id");
                                        DB::useDB('orderapp_b2b');
                                        $voteTimings = DB::query("select * from vote_timings where company_id = '".$_SESSION['company_id']."'");
                                        //$b2bRestDiscounts = getAllB2BRestDiscounts();
                                        foreach ($voteTimings as $voteTiming)
                                        {

                                            ?>
                                            <tr>
                                                <td><?=$voteTiming['id']?></td>
                                                <td><?=$voteTiming['voting_start']?></td>
                                                <td><?=$voteTiming['voting_end']?></td>
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