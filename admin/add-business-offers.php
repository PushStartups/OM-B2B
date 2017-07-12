<?php
include "header.php";
$menu_id = $_GET['id'];
$categories  =  getAllCategories($menu_id);

?>

<div id="main" role="main">

    <!-- MAIN CONTENT -->
    <div id="content">
        <!-- row -->
        <div class="row">

            <!-- col -->
            <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-cutlery "></i> Add A Business Offer</h1>
            </div>

        </div>
        <div id="myform">
            <section id="widget-grid">
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

                                    <form id="my-form"  method="post" enctype="multipart/form-data">
                                        <fieldset>
                                            <input name="authenticity_token" type="hidden">

                                            <div class="form-group">
                                                <label> Select Category </label>
                                                <select id="business_category" name="business_category" class="form-control" onchange="category_change(this.value)">
                                                    <option value="" disabled selected>Select Category</option>
                                                    <?php foreach($categories as $category)
                                                    { ?>
                                                        <option value="<?=$category['id']?>"><?=$category['name_en']?></option>

                                                    <?php }
                                                    ?>


                                                </select>
                                                <span style="font-size: 14px; color: red; width: 100%; padding: 9px;text-transform: none;"></span>
                                            </div>

                                            <div style="display:none" id="item-div" class="form-group">

                                            </div>

                                            <div id="week-day-div" style="display:none" class="form-group">
                                                <label>Select Day</label>
                                                <select id="day" name="day" class="form-control">
                                                    <option value="Sunday" selected>Sunday</option>
                                                    <option value="Monday">Monday</option>
                                                    <option value="Tuesday">Tuesday</option>
                                                    <option value="Wednesday">Wednesday</option>
                                                    <option value="Thursday">Thursday</option>
                                                </select>
                                            </div>

                                            <div id="week-cycle-div" style="display:none" class="form-group">
                                                <label>Week Cycle</label>
                                                <select id="week_cycle" name="week_cycle" class="form-control">
                                                    <option value="1" selected>1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>

                                                </select>
                                            </div>



                                            <br>

                                        </fieldset>
                                        <div style="display:none" id="business-offer-div" class="form-actions">
                                            <div onclick="add_business_offer('<?=$_SERVER['REQUEST_URI']?>')" class="btn btn-primary btn-lg">
                                                <i class="fa fa-save"></i>
                                                Submit
                                            </div>
                                            <!--                                            <input type="submit" value="Submit" class="btn btn-primary btn-lg">-->
                                        </div>
                                    </form>

                                </div>
                                <!-- end widget content -->
                            </div>
                            <!-- end widget div -->
                        </div>

                    </article>
                    <!-- WIDGET END -->
                </div>


                <?php
                foreach($categories as $cat)
                {
                    $row[] = $cat['id'];
                }
                //$qry1 = " select  * from  restaurants where id not in('" . implode("','", $row) . "') ";

                $business  =  DB::query("select * from business_lunch_detail where category_id IN ('" . implode("','", $row) . "') ");
                if(DB::count() > 0)
                {

                ?>
                <div class="row">
                    <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                        <div class="jarviswidget jarviswidget-color-darken" id="wid-id-0" data-widget-editbutton="false">

                            <header>
                                <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                                <h2>Extras</h2>
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

                                            <th data-hide="phone"> Category</th>
                                            <th data-hide="phone"> Item </th>
                                            <th data-hide="phone"> Week Day </th>
                                            <th data-hide="phone"> Week Cycle </th>

                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php

                                        foreach($business as $busines)
                                        {
                                            $items = DB::queryFirstRow("select * from items where id = '".$busines['item_id']."'");
                                            $category = DB::queryFirstRow("select * from categories where id = '".$busines['category_id']."'");

                                            ?>
                                            <tr>
                                                <td><?=$items['name_en']?></td>
                                                <td><?=$category['name_en']?></td>
                                                <td><?=$busines['week_day']?></td>
                                                <td><?=$busines['week_cycle']?></td>
                                            </tr>
                                        <?php
                                        }
                                        ?>

                                        </tbody>
                                    </table>
                                </div>

                            </div>

                        </div>
                    </article>

                </div>
                <?php } ?>
                <!-- end row -->
            </section>
        </div>
        <!-- end widget grid -->
    </div>
    <!-- END MAIN CONTENT -->
</div>




<div id="divBackground" style="position: fixed; z-index: 999; height: 100%; width: 100%;
        top: 0; left:0; background-color: Black; filter: alpha(opacity=60); opacity: 0.6; -moz-opacity: 0.8;display:none">
</div>
<!-- END MAIN PANEL -->
<?php
include "footer.php";
?>
