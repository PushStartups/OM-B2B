<?php
include "header.php";
$rolee = $_SESSION['b2b_admin_role'];
?>
<div id="main" role="main">
    <?php
    if(isset($_GET['id']))
    {
        $restaurant_id      =   $_GET['id'];
        $restaurant_name    =   getRestaurantName($restaurant_id);
        $kasruts               =   getKashrutsOfSpecificRestaurant($restaurant_id);

    }
    ?>

    <!-- MAIN CONTENT -->
    <div id="content">
        <!-- row -->
        <div class="row">

            <!-- col -->
            <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-tags "></i> <?=$restaurant_name?> </h1>
            </div>
            <!-- end col -->

            <!-- right side of the page with the sparkline graphs -->
            <!-- col -->
            <?php if ($rolee == 1) {?>
            <div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">

                <a onclick="add_kashrut_tab()" style="float:right"  class="btn btn-lg bg-color-purple txt-color-white"><i class="fa-fw fa fa-plus "></i> Add New Kashrut</a>

            </div>
            <?php } ?>
            <!-- end col -->

        </div>


        <section id="widget-grid" class="">
            <!-- row -->
            <div class="row">
                <!-- NEW WIDGET START -->
                <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                    <div style="display:none" class="jarviswidget" id="add_kash" data-widget-colorbutton="false" data-widget-editbutton="false">

                        <div>
                            <div class="widget-body">

                                <form>
                                    <fieldset>
                                        <input name="authenticity_token" type="hidden">
                                        <div class="form-group">
                                            <label>Kashrut Name</label>
                                            <input class="form-control" id="kashrut_name_en" name="kashrut_name_en" placeholder="Enter Kashrut Name" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error-tag-name-en"></span>
                                        </div>
                                        <div class="form-group">
                                            <label>Kashrut Name He</label>
                                            <input style="direction:RTL;" class="form-control" id="kashrut_name_he" name="kashrut_name_he"  type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error-tag-name-he"></span>
                                        </div>
                                    </fieldset>
                                    <div class="form-actions">
                                        <div onclick="add_kashrut_restaurant('<?=$restaurant_id?>','<?=$_SERVER['REQUEST_URI']?>')" class="btn btn-primary">
                                            <i class="fa fa-save"></i>
                                            Save
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    <?php if(!empty($kasruts)){  ?>
                        <!-- Widget ID (each widget will need unique ID)-->
                        <div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-1" data-widget-editbutton="false">

                            <header>
                                <span class="widget-icon"> <i class="fa fa-tags"></i> </span>
                                <h2>Restaurant Kashruts </h2>
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

                                            <th >Kashrut Name EN</th>
                                            <th >Kashrut Name HE</th>
                                            <th >Delete</th>
                                            <!--                                            <th>Delete</th>-->
                                        </tr>
                                        </thead>

                                        <tbody id="content">
                                        <?php

                                        foreach ($kasruts as $kasrut)
                                        {
                                            ?>
                                            <tr>

                                                <td><?=$kasrut['name_en']?></td>
                                                <td><?=$kasrut['name_he']?></td>
                                                <td><a onclick="delete_kasrut('<?=$kasrut['id']?>','<?=$_SERVER['REQUEST_URI']?>')"><button class="btn btn-labeled btn-danger txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-trash-o"></i> Delete</button></a></td>

                                            </tr>
                                        <?php } ?>
                                        </tbody>

                                    </table>

                                </div>
                                <!-- end widget content -->

                            </div>
                            <!-- end widget div -->

                        </div>
                    <?php  } else { echo "<h2>No Kashrut Found For This Restaurant</h2>"; } ?>
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
<script>

</script>
<!-- END MAIN PANEL -->
<?php
include "footer.php";
?>
