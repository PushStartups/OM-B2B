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
                    <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-user "></i>B2B Users</h1>
                </div>
                <!-- end col -->

                <!-- right side of the page with the sparkline graphs -->
                <!-- col -->
                <div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">
                    <!-- sparks -->

                    <!-- end sparks -->
                </div>
                <!-- end col -->

            </div>
            <br>
            <div align="center">
                <a style="text-decoration: none" href="add-new-user.php"><div class="btn btn-purple btn-lg">
                        <i class="fa fa-plus"></i>
                        Add B2B User
                    </div></a>
            </div><br><br>

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
                                <h2>B2B Users Detail </h2>
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
                                            <th data-class="expand">ID</th>
                                            <th >Email</th>
                                            <th >Address</th>
                                            <th >Discount</th>
                                            <th >Contact</th>
                                            <th >Language</th>
                                            <th >Action</th>
                                            <th >Delete</th>
                                            <!--                                            <th>Delete</th>-->
                                        </tr>
                                        </thead>

                                        <tbody id="content">
                                        <?php
                                        DB::useDB('orderapp_b2b_wui');
                                        $users = DB::query("select * from b2b_users");
                                        foreach ($users as $user)
                                        {
                                            ?>
                                            <tr>
                                                <td><?=$user['id']?></td>
                                                <td><?=$user['smooch_id']?></td>
                                                <td><?=$user['address']?></td>
                                                <td><?=$user['discount']?></td>
                                                <td><?=$user['contact']?></td>
                                                <td><?=$user['language']?></td>
                                                <td><a href="user-edit.php?user_id=<?=$user['id']?>"><button class="btn btn-labeled btn-primary bg-color-blueDark txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-info"></i> Edit </button></a></td>
                                                <td><a onclick="delete_user_db('<?=$user['id']?>','<?=$_SERVER['REQUEST_URI']?>')"><button class="btn btn-labeled btn-danger txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-trash-o"></i> Delete</button></a></td>

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