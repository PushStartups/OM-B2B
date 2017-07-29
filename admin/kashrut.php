<?php
include "header.php";

$rolee = $_SESSION['b2b_admin_role'];
?>

    <!-- MAIN PANEL -->
    <div id="main" role="main">


        <!-- MAIN CONTENT -->
        <div id="content">

            <!-- row -->
            <div class="row">

                <!-- col -->
                <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                    <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-tags "></i>Kashrut</h1>
                </div>
                <!-- end col -->

                <!-- right side of the page with the sparkline graphs -->
                <!-- col -->
                <?php if ($rolee == 1) {?>
                    <div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">
                        <!-- sparks -->
                        <a style="float:right" href="add-kashrut.php" class="btn btn-lg bg-color-purple txt-color-white"><i class="fa-fw fa fa-plus "></i> Add New Kashrut</a>

                        <!-- end sparks -->
                    </div>
                    <!-- end col -->
                <?php }?>

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
                                <h2>Kashruts </h2>
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

                                            <th >Tag Name EN</th>

                                            <th data-hide="phone, tablet">Tag Name HE</th>

                                            <?php if ($rolee == 1) {?>

                                                <th >Edit</th>

                                                <th >Delete</th>
                                            <?php }?>
                                            <!--                                            <th>Action</th>-->
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?php $kasruts = getAllKashrut();
                                        foreach ($kasruts as $kasrut)
                                        {

                                            ?>
                                            <tr>
                                                <td><?=$kasrut['id']?></td>

                                                <td><?=$kasrut['name_en']?></td>

                                                <td><?=$kasrut['name_he']?></td>

                                                <?php if ($rolee == 1) {?>

                                                    <td><a href="edit-kashrut.php?id=<?=$kasrut['id']?>"><button class="btn btn-labeled btn-primary bg-color-blueDark txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-edit"></i> Edit </button></a></td>

                                                    <td><a onclick="delete_kashrut('<?=$kasrut['id']?>','<?=$_SERVER['REQUEST_URI']?>')"><button class="btn btn-labeled btn-danger txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-trash-o"></i> Delete</button></a></td>
                                                <?php }?>

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