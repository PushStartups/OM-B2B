<?php
include "header.php";
?>
<div id="main" role="main">
    <div id="content">
        <div class="row">
            <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-key "></i> Reset Password </h1>
            </div>
        </div>
        <section id="widget-grid" class="">
            <!-- row -->
            <div class="row">
                <!-- NEW WIDGET START -->
                <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                    <div class="jarviswidget" id="wid-id-2" data-widget-colorbutton="false" data-widget-editbutton="false">

                        <div>
                            <div class="widget-body">
                                <form>
                                    <fieldset>
                                        <input name="authenticity_token" type="hidden">
                                        <div class="form-group">
                                            <label>Password</label>
                                            <input class="form-control" id="password" name="password" placeholder="Enter Password" type="password" required>
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error-password"></span>
                                        </div>
                                        <div class="form-group">
                                            <label>Confirm Password</label>
                                            <input class="form-control" id="retype-password" name="retype-password" placeholder="Retype Password" type="password" required>
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error-password-retype"></span>
                                        </div>
                                    </fieldset>
                                    <div class="form-actions">
                                        <div onclick="reset_password('<?=$_SESSION['user_id']?>')"  class="btn btn-primary">
                                            <a  style="text-decoration: none; color:white"><i class="fa fa-save"></i>
                                            Save</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- end widget -->
                </article>
            </div>
        </section>
    </div>
</div>

<!-- END MAIN PANEL -->
<?php
include "footer.php";
?>
