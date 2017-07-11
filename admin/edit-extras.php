<?php
include "header.php";

if(isset($_GET['id']))
{
    $extra_id                 =    $_GET['id'];
    $extra                    =    getExtra($extra_id);

}
else
{
    header("location:logout.php");
}
?>
<div id="main" role="main">

    <!-- MAIN CONTENT -->
    <div id="content">
        <!-- row -->
        <div class="row">

            <!-- col -->
            <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-cutlery "></i> Update Extras</h1>
            </div>

        </div>
        <div id="myform">
            <section id="widget-grid"  id="myform">
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

                                    <form>
                                        <fieldset>
                                            <input name="authenticity_token" type="hidden">

                                            <div class="form-group">
                                                <label>Name EN</label>
                                                <input class="form-control" id="name_en" name="name_en" value="<?=$extra['name_en']?>" placeholder="Enter Extras" type="text">
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="name_en_error"></span>
                                            </div>

                                            <div class="form-group">
                                                <label dir="rtl">NAME HE</label>
                                                <input style="direction:RTL;" class="form-control" id="name_he" name="name_he"  value="<?=$extra['name_he']?>" type="text">
                                                <span style="direction:RTL;font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="name_he_error"></span>
                                            </div>

                                            <div class="form-group">
                                                <label>Type</label>
                                                <select id="type" name="type" class="form-control">
                                                    <?php if($extra['type'] == "Multiple"){  ?>
                                                        <option value="Multiple" selected>Multiple</option>
                                                        <option value="One">One</option>
                                                    <?php } else { ?>
                                                    <option value="One"  selected>One</option>
                                                    <option value="Multiple">Multiple</option>
                                                    <?php } ?>

                                                </select>
                                                <span style="font-size: 14px; color: red; width: 100%; padding: 9px;text-transform: none;"></span>
                                            </div>

                                            <div class="form-group">
                                                <label>Limit</label>
                                                <input class="form-control" id="limit" name="limit" value="<?=$extra['limit']?>" placeholder="Enter Limit" type="text">
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="limit_error"></span>
                                            </div>

                                            <div class="form-group">
                                                <label>Price Replace</label>
                                                <select id="price_replace" name="price_replace" class="form-control">

                                                    <?php if($extra['type'] == 0){  ?>
                                                        <option value="0" selected>0</option>
                                                        <option value="1">1</option>
                                                    <?php } else { ?>
                                                        <option value="1" selected>1</option>
                                                        <option value="0">0</option>

                                                    <?php } ?>

                                                </select>
                                                <span style="font-size: 14px; color: red; width: 100%; padding: 9px;text-transform: none;"></span>
                                            </div>


                                        </fieldset>
                                        <div class="form-actions">
                                            <div onclick="update_extras('<?=$extra_id?>','<?=$_SERVER['REQUEST_URI']?>')" class="btn btn-primary btn-lg">
                                                <i class="fa fa-save"></i>
                                                Update
                                            </div>
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
