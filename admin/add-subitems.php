<?php
include "header.php";

if(isset($_GET['id']))
{
    $extra_id                   =    $_GET['id'];
    $extra_name                 =    getExtraName($extra_id);
    $subItems                   =    getSubItemsFromItemId($extra_id);
    $_SESSION['subitem_url']    =    $_SERVER['REQUEST_URI'];
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
                <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-briefcase "></i> Add SubItems To <?=$extra_name?> <b>(Restaurant: <?=$_SESSION['r_name']?>)</b></h1>
            </div>

        </div>
        <br>
        <div align="left">
            <form  action="import-subitems.php" method="post"  enctype="multipart/form-data">
                <fieldset>

                    <div class="form-group">
                        <label>Import Subitems Through CSV</label>
                        <input class="form-control" id="file" name="file"  type="file">
                        <input type="hidden" value="<?=$extra_id?>" name="extra_id" id="extra_id">
                        <input type="hidden" value="<?=$_SERVER['REQUEST_URI']?>" name="url" id="url">

                    </div>

                </fieldset>
                <button name="Import" type="submit" class="btn btn-primary"  data-loading-text="Loading...">
                    Import CSV File
                </button><br>
                *Please see the sample CSV file link. <a target="_blank" href="https://docs.google.com/spreadsheets/d/15DeOW-ZHI734O0F22juE5kEuffymjfTQCzmG00h1uqE/edit?usp=sharing">Click Here</a>
            </form>
        </div><br><br>

        <!-- widget grid -->

        <section id="widget-grid"  id="myform">

            <!-- SHOW CATEGORIES-->
            <?php  if(!empty($subItems)) { ?>
                <div class="row">
                    <!-- NEW WIDGET START -->
                    <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                        <div class="jarviswidget jarviswidget-color-darken" id="wid-id-0" data-widget-editbutton="false">

                            <header>
                                <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                                <h2>SubItems</h2>
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
                                            <th data-hide="phone"> Price </th>
                                            <th data-hide="phone,tablet"> Edit</th>
                                            <th data-hide="phone,tablet"> Delete</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php

                                        foreach( $subItems as $subItem) {
                                            ?>
                                            <tr>
                                                <td><?=$subItem['id']?></td>
                                                <td><?=$subItem['name_en']?></td>
                                                <td><?=$subItem['name_he']?></td>
                                                <td><?=$subItem['price']?></td>
                                                <td><a href="edit-subitems.php?id=<?=$subItem['id']?>"><button class="btn btn-labeled btn-primary bg-color-blueDark txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-edit"></i> Edit </button></a></td>
                                                <td><a onclick="delete_subitem('<?=$subItem['id']?>','<?=$_SERVER['REQUEST_URI']?>')"><button class="btn btn-labeled btn-danger txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-trash-o"></i> Delete</button></a></td>
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
            <?php  }  ?>

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
                                <div onclick="window.location.href = '<?=$_SESSION['extras_url']?>'" class="btn btn-success btn-lg">
                                    <i class="fa fa-arrow-left"></i>
                                    Go Back
                                </div>
                                <div onclick="show_subitems_div()" class="btn btn-primary btn-lg">
                                    <i class="fa fa-plus"></i>
                                    Add SubItems
                                </div>
                                <br><br>
                                <div id="add-subitem" style="display: none">
                                    <form>
                                        <fieldset>
                                            <input name="authenticity_token" type="hidden">

                                            <div class="form-group">
                                                <label>Name EN</label>
                                                <input class="form-control" id="name_en" name="name_en" placeholder="Enter SubItem" type="text">
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="name_en_error"></span>
                                            </div>
                                            <div class="form-group">
                                                <label dir="rtl">NAME HE</label>
                                                <input style="direction:RTL;" class="form-control" id="name_he" name="name_he"  type="text">
                                                <span style="direction:RTL;font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="name_he_error"></span>
                                            </div>



                                            <div class="form-group">
                                                <label>Price</label>
                                                <input class="form-control" id="price" name="price" placeholder="Enter Price" type="text">
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="price_error"></span>
                                            </div>


                                        </fieldset>
                                        <div class="form-actions">
                                            <div onclick="add_subitems('<?=$extra_id?>','<?=$_SERVER['REQUEST_URI']?>')" class="btn btn-primary btn-lg">
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
