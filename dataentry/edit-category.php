<?php
include "header.php";

if(isset($_GET['id']))
{
    $category_id                 =    $_GET['id'];
    $category                    =    getCategory($category_id);

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
                <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-cutlery "></i> Update Category</h1>
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
                                <script>
                                    globalEditCategoryLogo = null;
                                    document.getElementById('hidden_image').value = 0;
                                    //alert(globalEditLogo);
                                    function previewEditFileCategory()
                                    {

                                        var file    = document.querySelector('input[type=file]').files[0];
                                        var reader  = new FileReader();

                                        reader.onload = function (e) {

                                            $('#edit_logo1_category').attr('src', e.target.result);
                                            document.getElementById('hidden_image').value = 1;
                                        }

                                        reader.addEventListener("load", function () {

                                            globalEditCategoryLogo = reader.result;
                                            // alert(globalEditLogo);

                                        }, false);

                                        if (file) {
                                            reader.readAsDataURL(file);
                                        }
                                    }
                                </script>
                                <div class="widget-body">

                                    <form id="category-form" method="post" enctype="multipart/form-data">
                                        <fieldset>
                                            <input name="authenticity_token" type="hidden">

                                            <input id="path1" name="editorImagePath1" type = "hidden" >

                                            <div class="form-group">
                                                <label>Category Mobile Logo</label>
                                                <input class="form-control" name="logo1" id="file" onchange="previewEditFileCategory();"  type="file">
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="logo_error"></span>
                                                <img style="display:block" id="edit_logo1_category" src="<?=$category['image_url'] ?>" alt="" width="440" height="100" />
                                            </div>

                                            <div class="form-group">
                                                <label>Category Name</label>
                                                <input class="form-control" id="name_en" name="name_en" value="<?=$category['name_en']?>" placeholder="Enter Category" type="text">
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="name_en_error"></span>
                                            </div>
                                            <div class="form-group">
                                                <label dir="rtl">שם קטגוריה</label>
                                                <input style="direction:RTL;" class="form-control" id="name_he" name="name_he" value="<?=$category['name_he']?>" type="text">
                                                <span style="direction:RTL;font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="name_he_error"></span>
                                            </div>
                                            <input class="form-control" type="hidden" name="category_id" id="category_id" value="<?=$category_id ?>">
                                            <input class="form-control" type="hidden" name="hidden_image" id="hidden_image" value="">
                                            <div class="form-group">
                                                <label>Business Offer</label>
                                                <select id="business_offer" name="business_offer" class="form-control">
                                                    <?php if($extra['business_offer'] == 0){  ?>
                                                        <option value="0" selected>No</option>
                                                        <option value="1">Yes</option>
                                                    <?php } else { ?>
                                                        <option value="1" selected>Yes</option>
                                                        <option value="0">No</option>

                                                    <?php } ?>
                                                </select>
                                                <span style="font-size: 14px; color: red; width: 100%; padding: 9px;text-transform: none;"></span>
                                            </div>

                                        </fieldset>
                                        <div class="form-actions">

                                            <input type="submit" class="btn btn-lg" value="Update">
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
