<?php
include "header.php";
echo  $users_id                 =    $_GET['user_id'];
if(isset($_GET['user_id']))
{


    $users                    =    getSpecificUsers($users_id);

$company = $users['company_id'];
$company_name = getSpecificCompanies($company);

}
else
{
    header("location:logout.php");
}
?>




<div id="main" role="main">
    <!-- MAIN CONTENT -->
    <div id="content">


        <section id="widget-grid" class="">

            <div class="row">
                <!-- col -->
                <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                    <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-users "></i> Edit <?=$company_name['name'];?> User</h1>
                </div>

            </div>

            <div class="row">
                <!-- NEW WIDGET START -->
                <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">


                        <div  class="jarviswidget jarviswidget-color-darken" id="wid-id-3" data-widget-editbutton="false">
                            <div>
                                <!-- widget edit box -->
                                <div class="jarviswidget-editbox">
                                    <!-- This area used as dropdown edit box -->

                                </div>
                                <!-- end widget edit box -->

                                <!-- widget content -->
                                <form method="post">
                                    <fieldset>


                                        <div class="form-group">
                                            <label>Name</label>
                                            <input class="form-control" id="name" name="name" placeholder="Enter Name" type="text" value="<?=$users['name'];?>">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_name"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Email</label>
                                            <input class="form-control" id="email_smooch_id" name="email_smooch_id" placeholder="Enter Email" type="text" value="<?=$users['smooch_id'];?>">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_email_smooch_id"></span>
                                        </div>



                                        <div class="form-group">
                                            <label>Contact</label>
                                            <input class="form-control" id="contact" name="contact" placeholder="Enter Contact" type="text" value="<?=$users['contact'];?>">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_contact"></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Address</label>
                                            <input class="form-control" id="address" name="address" placeholder="Enter Address" type="text" value="<?=$users['address'];?>">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_address"></span>
                                        </div>

                                        <input type="hidden" name="users_id" id="users_id" value="<?=$users_id;?>"

                                    </fieldset>
                                    <div class="form-actions">
                                        <div onclick="edit_user('<?=$_SERVER['REQUEST_URI']?>')" class="btn btn-primary btn-lg">
                                            <i class="fa fa-save"></i>
                                            Submit
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>


                </article>

            </div><br>


            <!-- end row -->

        </section>


        <!-- end widget grid -->

    </div>
    <!-- END MAIN CONTENT -->

</div>


<!-- END MAIN PANEL -->
<?php
include "footer.php";
?>
