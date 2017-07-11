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
                    <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-user "></i>Manage Users</h1>
                </div>
                <!-- end col -->

                <!-- right side of the page with the sparkline graphs -->
                <!-- col -->
                <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                    <a style="text-decoration: none" href="add-a-user.php"><div class="btn btn-purple btn-lg"><i class="fa-fw fa fa-plus "></i> Add A User</div></a>
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
                        <div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-3" data-widget-editbutton="false">

                            <header>
                                <span class="widget-icon"> <i class="fa fa-tags"></i> </span>
                                <h2><?=$_SESSION['company_name']?> Company Users </h2>
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

                                    <table id="datatable_tabletools" class="table table-striped table-bordered" width="100%">

                                        <thead>

                                        <tr>
                                            <th data-class="expand">Username</th>

                                            <th >Discount</th>

                                            <th data-hide="phone, tablet">Contact</th>
                                            <th data-hide="phone, tablet">Address</th>
                                            <th data-hide="phone, tablet">View Orders</th>

                                            <th>Action</th>
                                            <th>Delete</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?php
                                        //  DB::query("select brd.*,c.name,r.name_en from b2b_rest_discounts as brd inner join restaurants as r on brd.rest_id = r.id  inner join company as c on brd.company_id = c.id");
                                        DB::useDB('orderapp_b2b');
                                        $users = DB::query("select * from b2b_users where company_id = '".$_SESSION['company_id']."'");
                                        //$b2bRestDiscounts = getAllB2BRestDiscounts();
                                        foreach ($users as $company)
                                        {
                                            ?>
                                            <tr>
                                                <td><?=$company['user_name']?></td>

                                                <td><?=$company['discount']?></td>

                                                <td><?=$company['contact']?></td>

                                                <td><?=$company['address']?></td>
                                                <td><a href="view-orders.php?id=<?=$company['id']?>"><button class="btn btn-labeled btn-primary bg-color-blueDark txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-eye"></i> View Orders </button></a></td>

                                                <td><a href="edit-user.php?id=<?=$company['id']?>"><button class="btn btn-labeled btn-primary bg-color-blueDark txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-edit"></i> Edit </button></a></td>
                                                <td><a onclick="delete_user('<?=$company['id']?>','<?=$_SERVER['REQUEST_URI']?>')"><button class="btn btn-labeled btn-danger txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-trash-o"></i> Delete</button></a></td>
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