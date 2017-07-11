<?php
include "header.php";
?>
<div id="main" role="main">
<?php
if(isset($_GET['companies_id']))
{
    $companies_id = $_GET['companies_id'];
    $company_name = getCompanyName($companies_id);
    $company_rest_limit = 2;
    //$restaurants = getRestaurantsOfSpecificCompany($companies_id);
}

?>
    <script>
        var rest_limit = '<?=$company_rest_limit?>';
    </script>
    <!-- MAIN CONTENT -->
    <div id="content">

        <!-- row -->
        <div class="row">

            <!-- col -->
            <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-briefcase "></i> <?=$company_name?> </h1>
            </div>
            <!-- end col -->

            <!-- right side of the page with the sparkline graphs -->
            <!-- col -->
            <div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">

                <a onclick="add_restaurant_tab()" style="float:right"  class="btn btn-lg bg-color-purple txt-color-white"><i class="fa-fw fa fa-plus "></i> Add New Restaurant</a>

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

                    <div style="display:none" class="jarviswidget" id="wid-id-2" data-widget-colorbutton="false" data-widget-editbutton="false">



                        <div>

                            <div class="widget-body">

                                <form method="post" enctype="multipart/form-data" action="ajax/insert_company_restaurant.php">
                                    <fieldset>
                                        <input name="authenticity_token" type="hidden">
                                        <div class="form-group">
                                            <label>Restaurant Name</label>
<!--                                            <input class="form-control" id="rest_name" name="rest_name" placeholder="Enter Restaurant Name" type="text">-->
                                            <select id="rest_name" name="rest_name[]" multiple="multiple" class="multiselect-ui form-control" required>
                                                <?php


                                                DB::useDB('orderapp_b2b_wui');
                                                $rest_ids = DB::query("SELECT *  FROM company_rest WHERE company_id =  '$companies_id'");



                                                foreach ($rest_ids as $r) {
                                                    $row[] = $r['rest_id'];
                                                }
                                                DB::useDB('orderapp_restaurants_b2b_wui');
                                                $qry1 = " select  * from  restaurants where id not in('" . implode("','", $row) . "') ";

                                                $restaurant = db::query($qry1);

                                                    foreach($restaurant as $rest)
                                                    { ?>
                                                        <option value="<?=$rest['id']?>"><?=$rest['name_en']?></option>;
                                                    <?php
                                                    }
                                                ?>



                                            </select>
                                            <input type="hidden" value="<?=$companies_id?>" id="company_id" name="company_id">
                                            <input type="hidden" value="<?=$_SERVER['REQUEST_URI']?>" id="url" name="url">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error-name"></span>
                                        </div>
                                    </fieldset>
                                    <div class="form-actions">

                                    <input type="submit" value="Submit" class="btn btn-primary">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

<!--                    $restaurants = DB::query("select company_rest.*, restaurants.name_en as restaurants_name from company_rest inner join restaurants on company_rest.rest_id = restaurants.id where company_id = '$company_id'");-->
                    <?php
                    DB::useDB('orderapp_b2b_wui');
                    $restaurants  = DB::query("select * from company_rest where company_id = '$companies_id'");


                    ?>

                    <?php if(!empty($restaurants)){  ?>
                    <!-- Widget ID (each widget will need unique ID)-->
                    <div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-1" data-widget-editbutton="false">

                        <header>
                            <span class="widget-icon"> <i class="fa fa-cutlery"></i> </span>
                            <h2>Restaurants </h2>
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
                                        <th data-class="expand">Restaurant ID</th>
                                        <th >Restaurant Name</th>
                                        <th>Delete</th>
                                    </tr>
                                    </thead>

                                    <tbody id="content">
                                    <?php


                                    foreach($restaurants as $restaurant)
                                    {
                                        DB::useDB('orderapp_restaurants_b2b_wui');
                                        $rest = DB::queryFirstRow("select * from restaurants where id = '".$restaurant['rest_id']."'");
                                        ?>
                                        <tr>
                                            <td><?=$restaurant['rest_id']?></td>
                                            <td><?=$rest['name_en']?></td>
                                            <td><a onclick="delete_company_restaurant('<?=$restaurant['rest_id']?>','<?=$restaurant['company_id']?>','<?=$_SERVER['REQUEST_URI']?>')"><button class="btn btn-labeled btn-danger  txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-trash-o"></i> Delete </button></a></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>

                                </table>

                            </div>
                            <!-- end widget content -->

                        </div>
                        <!-- end widget div -->

                    </div>
                    <?php  } else { echo "<h2>No Restaurant Found In This Company</h2>"; } ?>
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
