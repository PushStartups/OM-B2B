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
                <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-building "></i> Add A Company Restaurant Discount </h1>
            </div>

        </div>
        <div id="myform">
            <section id="widget-grid" >
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
                                                <label>Company</label>
                                                <input class="form-control" id="company" name="company" placeholder="Enter Company Name" type="text">
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="company_error"></span>
                                            </div>

                                            <div class="form-group">
                                                <label>Restaurant</label>
                                                <input class="form-control" id="restaurant" name="restaurant" placeholder="Enter Restaurant Name" type="text">
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="restaurant_error"></span>
                                            </div>

                                            <div class="form-group">
                                                <label>Discount</label>
                                                <input class="form-control" id="discount" name="discount" placeholder="Enter Discount in Percentage" type="text">
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="discount_error"></span>
                                            </div>
                                            

                                            <br>
                                        </fieldset>
                                        <div class="form-actions">
                                            <div onclick="add_rest_company_discount('<?=$_SERVER['REQUEST_URI']?>')" class="btn btn-primary btn-lg">
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
                <div class="row">
                    <!-- a blank row to get started -->
                    <div class="col-sm-12">
                        <!-- your contents here -->
                    </div>

                </div>
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
