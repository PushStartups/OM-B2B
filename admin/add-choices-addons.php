<?php
include "header.php";

if(isset($_GET['id']))
{
    $item_id     =  $_GET['id'];
    $item_name   =  getItemName($item_id);
    $extras      =  getExtrasFromItemId($item_id);

    $_SESSION['extras_url']   =   $_SERVER['REQUEST_URI'];


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
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-briefcase "></i> Add Addons & Choices To <?=$item_name?> Item <b>(Restaurant: <?=$_SESSION['r_name']?>)</b></h1>
            </div>

        </div>

        <br>
        <div align="left">
            <form  action="import-extras.php" method="post"  enctype="multipart/form-data">
                <fieldset>

                    <div class="form-group">
                        <label>Import Extras Through CSV</label>
                        <input class="form-control" id="file" name="file"  type="file">
                        <input type="hidden" value="<?=$item_id?>" name="item_id" id="item_id">
                        <input type="hidden" value="<?=$_SERVER['REQUEST_URI']?>" name="url" id="url">

                    </div>

                </fieldset>
                <button name="Import" type="submit" class="btn btn-primary"  data-loading-text="Loading...">
                    Import CSV File
                </button><br>
                *Please see the sample CSV file link. <a target="_blank" href="https://docs.google.com/spreadsheets/d/1VObPfVvW9cVT9jPTijsHsanmIVhajdFuFbFdW5dCHZQ/edit">Click Here</a>
            </form>
        </div><br><br>

        <!-- widget grid -->

        <section id="widget-grid"  id="myform">

            <!-- SHOW CATEGORIES-->
            <?php  if(!empty($extras)) { ?>
                <div class="row">
                    <!-- NEW WIDGET START -->
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
                                            <th data-hide="phone">ID</th>

                                            <th data-hide="phone"> Name </th>
                                            <th data-hide="phone"> שֵׁם </th>
                                            <th data-hide="phone"> Type </th>
                                            <th data-hide="phone"> Price Replace </th>
                                            <th data-hide="phone"> Limit </th>
                                            <th data-hide="phone"> Add SubItems</th>
                                            <th data-hide="phone,tablet"> Edit</th>
                                            <th data-hide="phone,tablet"> Delete</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php

                                        foreach($extras as $extra) {
                                            ?>
                                            <tr>
                                                <td><?=$extra['id']?></td>
                                                <td><?=$extra['name_en']?></td>
                                                <td><?=$extra['name_he']?></td>
                                                <td><?=$extra['type']?></td>
                                                <td><?=$extra['price_replace']?></td>
                                                <td><?=$extra['limit']?></td>
                                                <td><a style="text-decoration: none" href="add-subitems.php?id=<?=$extra['id']?>"><button class="btn btn-labeled btn-success  txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-plus"></i> Add SubItems </button></a></td>
                                                <td><a href="edit-extras.php?id=<?=$extra['id']?>"><button class="btn btn-labeled btn-primary bg-color-blueDark txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-edit"></i> Edit </button></a></td>
                                                <td><a onclick="delete_extras('<?=$extra['id']?>','<?=$_SERVER['REQUEST_URI']?>')"><button class="btn btn-labeled btn-danger txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-trash-o"></i> Delete</button></a></td>

                                            </tr>
                                        <?php  }
                                        ?>

                                        </tbody>
                                    </table>
                                </div>

                            </div>

                        </div>
                    </article>

                </div>
                <!-- SHOW CATEGORIES END-->
            <?php  } ?>

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
                                <div onclick="window.location.href = '<?=$_SESSION['item_url']?>'" class="btn btn-success btn-lg">
                                    <i class="fa fa-arrow-left"></i>
                                    Go Back
                                </div>
                                <div onclick="show_addons_div()" class="btn btn-primary btn-lg">
                                    <i class="fa fa-plus"></i>
                                    Add Choices & Addons
                                </div>
                                <br><br>
                                <div id="add-choices" style="display: none">
                                    <form>
                                        <fieldset>
                                            <input name="authenticity_token" type="hidden">

                                            <div class="form-group">
                                                <label>Name EN</label>
                                                <input class="form-control" id="name_en" name="name_en" placeholder="Enter Extras" type="text">
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="name_en_error"></span>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label dir="rtl">NAME HE</label>
                                                <input style="direction:RTL;" class="form-control" id="name_he" name="name_he"  type="text">
                                                <span style="direction:RTL;font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="name_he_error"></span>
                                            </div>

                                            <div class="form-group">
                                                <label>Type</label>
                                                <select id="type" name="type" class="form-control">
                                                    <option value="Multiple" selected>Multiple</option>
                                                    <option value="One">One</option>
                                                </select>
                                                <span style="font-size: 14px; color: red; width: 100%; padding: 9px;text-transform: none;"></span>
                                            </div>

                                            <div class="form-group">
                                                <label>Limit</label>
                                                <input class="form-control" id="limit" name="limit" placeholder="Enter Limit" type="text">
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="limit_error"></span>
                                            </div>

                                            <div class="form-group">
                                                <label>Price Replace</label>
                                                <select id="price_replace" name="price_replace" class="form-control">
                                                    <option value="0" selected>0</option>
                                                    <option value="1">1</option>
                                                </select>
                                                <span style="font-size: 14px; color: red; width: 100%; padding: 9px;text-transform: none;"></span>
                                            </div>


                                        </fieldset>
                                        <div class="form-actions">
                                            <div onclick="add_extras('<?=$item_id?>','<?=$_SERVER['REQUEST_URI']?>')" class="btn btn-primary btn-lg">
                                                <i class="fa fa-save"></i>
                                                Submit
                                            </div>
                                        </div>
                                    </form>
                                </div>

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

        <!-- end widget grid -->
    </div>
    <!-- END MAIN CONTENT -->
</div>

</div>
<!-- END MAIN PANEL -->
<?php
include "footer.php";
?>
