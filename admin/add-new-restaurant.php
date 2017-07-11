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
                <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-cutlery "></i> Add A Restaurant</h1>
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

                                            <input id="path1" name="editorImagePath1" type = "hidden" >
                                            <div class="form-group">
                                                <label>Restaurant Logo</label>
                                                <input class="form-control" accept="image/*" name="logo" id="logo" onchange="previewFile();"  type="file">
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="logo_error"></span>
                                                <img style="display:block" id="new_image1" src="#" alt="" width="105" height="105" />
                                            </div>
                                            <div class="form-group">
                                                <label>Restaurant Name</label>
                                                <input class="form-control" id="name_en" name="name_en" placeholder="Enter Restaurant" type="text">
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="name_en_error"></span>
                                            </div>
                                            <div class="form-group">
                                                <label dir="rtl">שם המסעדה</label>
                                                <input style="direction:RTL;" class="form-control" id="name_he" name="name_he" placeholder="שם המסעדה" type="text">
                                                <span style="direction:RTL;font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="name_he_error"></span>
                                            </div>

                                            <div class="form-group">
                                                <label>Contact</label>
                                                <input class="form-control" id="contact" name="contact" placeholder="Enter Contact" type="text">
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="contact_error"></span>
                                            </div>

                                            <div class="form-group">
                                                <label>Minimum Amount</label>
                                                <input class="form-control" id="min_amount" name="min_amount" placeholder="Enter Amount" type="text">
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="min_amount_error"></span>
                                            </div>
                                            

                                            <div class="form-group">
                                                <label>Pickup</label>
                                                <select id="pickup_hide" name="pickup_hide" class="form-control">
                                                    <option value="1" selected>Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                                <span style="font-size: 14px; color: red; width: 100%; padding: 9px;text-transform: none;"></span>
                                            </div>

                                            <div class="form-group">
                                                <label>City </label>
                                                <select id="city" name="city" class="form-control">
                                                    <?php $city = getAllCities();
                                                    foreach($city as $cities)
                                                    { ?>
                                                        <option value = <?=$cities['id']?>><?=$cities['name_en']?></option>
                                                        <?php
                                                    }
                                                    ?>

                                                </select>
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;"></span>
                                            </div>

                                            <div class="form-group">
                                                <label> Coming Soon </label>
                                                <select id="coming_soon" name="coming_soon" class="form-control">
                                                    <option value="0" selected>Off</option>
                                                    <option value="1">On</option>

                                                </select>
                                                <span style="font-size: 14px; color: red; width: 100%; padding: 9px;text-transform: none;"></span>
                                            </div>

                                            <div class="form-group">
                                                <label>Restaurant Hide / Show </label>
                                                <select id="hide" name="hide" class="form-control">
                                                    <option value="0" selected>Show</option>
                                                    <option value="1">Hide</option>
                                                </select>
                                                <span style="font-size: 14px; color: red; width: 100%; padding: 9px;text-transform: none;"></span>
                                            </div>

                                            <div class="form-group">
                                                <label>Description </label>
                                                <textarea class="form-control" id="description_en" name="description_en" placeholder="Enter Description" type="text"></textarea>
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="description_en_error"></span>
                                            </div>
                                            <div class="form-group">
                                                <label dir="rtl">תיאור </label>
                                                <textarea style="direction:RTL;" class="form-control" id="description_he" name="description_he" placeholder="הזן תיאור בעברית" type="text"></textarea>
                                                <span style="direction:RTL;font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="description_he_error"></span>
                                            </div>

                                            <div class="form-group">
                                                <label>Address </label>
                                                <input class="form-control" id="address_en" name="address_en" placeholder="Enter Address in English" type="text">
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="address_en_error"></span>
                                            </div>
                                            <div class="form-group">
                                                <label dir="rtl">כתובת </label>
                                                <input style="direction:RTL;" class="form-control" id="address_he" name="address_he" placeholder="הזן כתובת בעברית" type="text">
                                                <span style="direction:RTL;font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="address_he_error"></span>
                                            </div>

                                            <div class="form-group">
                                                <label>Hechsher </label>
                                                <input class="form-control" id="hechsher_en" name="hechsher_en" placeholder="Enter Hechsher" type="text">
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="hechsher_en_error"></span>
                                            </div>
                                            <div class="form-group">
                                                <label dir="rtl">הכשרת </label>
                                                <input style="direction:RTL;" class="form-control" id="hechsher_he" name="hechsher_he" placeholder="הזן הכשרת" type="text">
                                                <span style="direction:RTL;font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="hechsher_he_error"></span>
                                            </div>
                                            <br>

                                        </fieldset>
                                        <div class="form-actions">
                                            <div onclick="add_restaurant(globalImg)" class="btn btn-primary btn-lg">
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



<script type="text/javascript">

    globalVal = 0;
    globalImg = null;
    var scroll_position ;


    $("#new_image1").hide();


    function previewFile() {

        var file    = document.querySelector('input[type=file]').files[0];
        var reader  = new FileReader();
        reader.onload = function (e) {

            $('#new_image1').attr('src', e.target.result);

        }

        reader.addEventListener("load", function () {

            globalImg = reader.result;

        }, false);

        if (file) {
            reader.readAsDataURL(file);
        }
    }

</script>

<div id="divBackground" style="position: fixed; z-index: 999; height: 100%; width: 100%;
        top: 0; left:0; background-color: Black; filter: alpha(opacity=60); opacity: 0.6; -moz-opacity: 0.8;display:none">
</div>
<!-- END MAIN PANEL -->
<?php
include "footer.php";
?>
