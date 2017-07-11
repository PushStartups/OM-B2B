<?php
include "header.php";

if(isset($_GET['id']))
{
    $category_id = $_GET['id'];

    $category_name          =   getCategoryName($category_id);
    $items                  =   getItemsFromCategoryId($category_id);

    $_SESSION['item_url']   =   $_SERVER['REQUEST_URI'];

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
                <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-briefcase "></i> Add Items To <?=$category_name?> Category <b>(Restaurant: <?=$_SESSION['r_name']?>)</b></h1>
            </div>

        </div>

        <br>
        <div align="left">
            <form  action="import-items.php" method="post"  enctype="multipart/form-data">
                <fieldset>

                    <div class="form-group">
                        <label>Import Items Through CSV</label>
                        <input class="form-control" id="file" name="file"  type="file">
                        <input type="hidden" value="<?=$category_id?>" name="category_id" id="category_id">
                        <input type="hidden" value="<?=$_SERVER['REQUEST_URI']?>" name="url" id="url">

                    </div>

                </fieldset>
                <button name="Import" type="submit" class="btn btn-primary"  data-loading-text="Loading...">
                    Import CSV File
                </button><br>
                *Please see the sample CSV file link. <a target="_blank" href="https://docs.google.com/spreadsheets/d/1XjDJjyNy70atEXFR9rrDs3tZDAp81xoaW3KPttMHOC4/edit#gid=0">Click Here</a>
            </form>
        </div><br><br>

        <!-- widget grid -->

        <section id="widget-grid"  id="myform">

            <!-- SHOW CATEGORIES-->
            <?php  if(!empty($items)) { ?>
                <div class="row">
                    <!-- NEW WIDGET START -->
                    <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                        <div class="jarviswidget jarviswidget-color-darken" id="wid-id-0" data-widget-editbutton="false">

                            <header>
                                <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                                <h2>Items</h2>
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

                                            <th data-hide="phone"><i class="fa-fw fa fa-info text-muted hidden-md hidden-sm hidden-xs"></i> Name </th>
                                            <th data-hide="phone"><i class="fa-fw fa fa-info text-muted hidden-md hidden-sm hidden-xs"></i> שֵׁם </th>
                                            <th data-hide="phone"><i class="fa-fw fa fa-tags text-muted hidden-md hidden-sm hidden-xs"></i> Hide/Show </th>
                                            <th data-hide="phone"><i class="fa-fw fa fa-tags text-muted hidden-md hidden-sm hidden-xs"></i> Price </th>
                                            <th data-hide="phone"><i class="fa-fw fa fa-plus text-muted hidden-md hidden-sm hidden-xs"></i> Add Choices & Addons </th>
                                            <th data-hide="phone,tablet"><i class="fa fa-fw fa-edit txt-color-blue hidden-md hidden-sm hidden-xs"></i> Edit</th>
                                            <th data-hide="phone,tablet"><i class="fa fa-fw fa-edit txt-color-blue hidden-md hidden-sm hidden-xs"></i> Delete</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php

                                        foreach($items as $item) {
                                            ?>
                                            <tr>

                                                <td><?=$item['id']?></td>
                                                <td><?=$item['name_en']?></td>
                                                <td><?=$item['name_he']?></td>
                                                <td><?=$item['hide']?></td>
                                                <td><?=$item['price']?></td>
                                                <td><a style="text-decoration: none" href="add-choices-addons.php?id=<?=$item['id']?>"><button class="btn btn-labeled btn-success  txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-plus"></i> Add Choices & Addons </button></a></td>
                                                <td><a href="edit-items.php?id=<?=$item['id']?>"><button class="btn btn-labeled btn-primary bg-color-blueDark txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-edit"></i> Edit </button></a></td>
                                                <td><a onclick="delete_item('<?=$item['id']?>','<?=$_SERVER['REQUEST_URI']?>')"><button class="btn btn-labeled btn-danger txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-trash-o"></i> Delete</button></a></td>

                                            </tr>
                                        <?php  } ?>

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
                                <div onclick="go_back_to_category('<?=$_SESSION['category_url']?>')" class="btn btn-success btn-lg">
                                    <i class="fa fa-arrow-left"></i>
                                    Go Back
                                </div>
                                <div onclick="show_category_div()" class="btn btn-primary btn-lg">
                                    <i class="fa fa-plus"></i>
                                    Add Items To <?=$category_name?>
                                </div>
                                <br><br>
                                <div id="add-category" style="display: none">
                                    <form>
                                        <fieldset>
                                            <input name="authenticity_token" type="hidden">

                                            <div class="form-group">
                                                <label>Item Name</label>
                                                <input class="form-control" id="name_en" name="name_en" placeholder="Enter Item" type="text">
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="name_en_error"></span>
                                            </div>
                                            <div class="form-group">
                                                <label dir="rtl">שם הפריט</label>
                                                <input style="direction:RTL;" class="form-control" id="name_he" name="name_he"  type="text">
                                                <span style="direction:RTL;font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="name_he_error"></span>
                                            </div>

                                            <div class="form-group">
                                                <label>Item Hide</label>
                                                <select id="hide" name="hide" class="form-control">
                                                    <option value="0" selected>No</option>
                                                    <option value="1">Yes</option>
                                                </select>
                                                <span style="font-size: 14px; color: red; width: 100%; padding: 9px;text-transform: none;"></span>
                                            </div>

                                            <div class="form-group">
                                                <label>Item Price</label>
                                                <input class="form-control" id="price" name="price" placeholder="Enter Price" type="text">
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="price_error"></span>
                                            </div>

                                            <div class="form-group">
                                                <label>Description </label>
                                                <textarea class="form-control" id="desc_en" name="desc_en" placeholder="Enter Description" type="text"></textarea>
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="desc_en_error"></span>
                                            </div>

                                            <div class="form-group">
                                                <label dir="rtl">תיאור </label>
                                                <textarea style="direction:RTL;" class="form-control" id="desc_he" name="desc_he" placeholder="הזן תיאור בעברית" type="text"></textarea>
                                                <span style="direction:RTL;font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="desc_he_error"></span>
                                            </div>

                                        </fieldset>
                                        <div class="form-actions">
                                            <div onclick="add_new_item('<?=$category_id?>','<?=$_SERVER['REQUEST_URI']?>')" class="btn btn-primary btn-lg">
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
