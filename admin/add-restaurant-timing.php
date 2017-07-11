<?php
include "header.php";

if(isset($_GET['id']))
{
     $restaurant_id = $_GET['id'];

    $timings = getAllTimings($restaurant_id);

    $count = 1;
    $week1[] ="";
    $week2[] ="";
    $week3[] ="";
    $week4[] ="";
    $week5[] ="";
    $week6[] ="";
    $week7[] ="";

    foreach ($timings as $time)
    {
        if($count == 1)
        {
            $week1['id']                    =  $time['id'];
            $week1['opening_time']          =   $time['opening_time'];
            $week1['closing_time']          =   $time['closing_time'];
        }
        if($count == 2)
        {
            $week2['id']                    =  $time['id'];
            $week2['opening_time']          =   $time['opening_time'];
            $week2['closing_time']          =   $time['closing_time'];
        }
        if($count == 3)
        {
            $week3['id']                    =  $time['id'];
            $week3['opening_time']          =   $time['opening_time'];
            $week3['closing_time']          =   $time['closing_time'];
        }
        if($count == 4)
        {
            $week4['id']                    =  $time['id'];
            $week4['opening_time']          =   $time['opening_time'];
            $week4['closing_time']          =   $time['closing_time'];
        }
        if($count == 5)
        {
            $week5['id']                    =  $time['id'];
            $week5['opening_time']          =   $time['opening_time'];
            $week5['closing_time']          =   $time['closing_time'];
        }
        if($count == 6)
        {
            $week6['id']                    =  $time['id'];
            $week6['opening_time']          =   $time['opening_time'];
            $week6['closing_time']          =   $time['closing_time'];
        }
        if($count == 7)
        {
            $week7['id']                    =  $time['id'];
            $week7['opening_time']          =   $time['opening_time'];
            $week7['closing_time']          =   $time['closing_time'];
        }
        $count++;

    }


    $delivery_address = getAllDeliveryAddress($restaurant_id);


}
else
{
    header("location:logout.php");
}





?>

<style>
    .logo-table {
        width: 220px;
        height: 50px;
    }
</style>

<div id="main" role="main">

    <!-- MAIN CONTENT -->
    <div id="content">
        <!-- row -->
        <div class="row">

            <!-- col -->
            <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-clock-o"></i> Add Restaurant Timings</h1>
            </div>

        </div>

        <!-- widget grid -->

        <section id="widget-grid">

            <div class="row">
                <!-- NEW WIDGET START -->
                <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                    <div class="jarviswidget jarviswidget-color-darken" id="wid-id-0" data-widget-editbutton="false">

                        <header>
                            <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                            <h2>Restaurants Timing</h2>
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

                                <form method="post">

                                    <table id="" class="table table-striped table-bordered" width="100%">

                                        <thead>

                                        <tr>
                                            <th data-class="expand">Day</th>

                                            <th >Start Time</th>

                                            <th>Close Time</th>

                                        </tr>
                                        </thead>

                                        <tbody>
                                        <tr>
                                            <td>
                                                Sunday
                                            </td>
                                            <td>
                                                <div class="input-group form-group clockpicker">
                                                    <input type="text" id="sunday_start_time" class="form-control" placeholder="Select Time"
                                                           value="<?php echo $week1['opening_time'];?>">
                                                    <input type="hidden" value="<?php echo $week1['id']; ?>" id="week1_id"/>
                                                    <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                            </span>

                                                </div>
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_sunday_start_time"></span>
                                            </td>
                                            <td>
                                                <div class="input-group form-group clockpicker">
                                                    <input type="text" id="sunday_end_time" class="form-control" placeholder="Select Time"
                                                           value="<?php echo $week1['closing_time'];?>">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>

                                                </div>
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_sunday_end_time"></span>

                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                Monday
                                            </td>
                                            <td>
                                                <div class="input-group form-group clockpicker">
                                                    <input type="text" id="monday_start_time" class="form-control" placeholder="Select Time"
                                                           value="<?php echo $week2['opening_time'];?>">
                                                    <input type="hidden" value="<?php echo $week2['id']; ?>" id="week2_id"/>
                                                    <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                            </span>
                                                </div>
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_monday_start_time"></span>

                                            </td>
                                            <td>
                                                <div class="input-group form-group clockpicker">
                                                    <input type="text" id="monday_end_time" class="form-control" placeholder="Select Time"
                                                           value="<?php echo $week2['closing_time'];?>">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>
                                                </div>
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_monday_end_time"></span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                Tuesday
                                            </td>
                                            <td>

                                                <div class="input-group form-group clockpicker">
                                                    <input type="text" id="tuesday_start_time" class="form-control" placeholder="Select Time"
                                                           value="<?php echo $week3['opening_time'];?>">
                                                    <input type="hidden" value="<?php echo $week3['id']; ?>" id="week3_id"/>
                                                    <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                            </span>
                                                </div>
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_tuesday_start_time"></span>
                                            </td>
                                            <td>
                                                <div class="input-group form-group clockpicker">
                                                    <input type="text" id="tuesday_end_time" class="form-control" placeholder="Select Time"
                                                           value="<?php echo $week3['closing_time'];?>">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>
                                                </div>
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_tuesday_end_time"></span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                Wednesday
                                            </td>
                                            <td>
                                                <div class="input-group form-group clockpicker">
                                                    <input type="text" id="wednesday_start_time" class="form-control" placeholder="Select Time"
                                                           value="<?php echo $week4['opening_time'];?>">
                                                    <input type="hidden" value="<?php echo $week4['id']; ?>" id="week4_id"/>
                                                    <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                            </span>
                                                </div>
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_wednesday_start_time"></span>

                                            </td>
                                            <td>
                                                <div class="input-group form-group clockpicker">
                                                    <input type="text" id="wednesday_end_time" class="form-control" placeholder="Select Time"
                                                           value="<?php echo $week4['closing_time'];?>">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>
                                                </div>
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_wednesday_end_time"></span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                Thursday
                                            </td>
                                            <td>
                                                <div class="input-group form-group clockpicker">
                                                    <input type="text" id="thursday_start_time" class="form-control" placeholder="Select Time"
                                                           value="<?php echo $week5['opening_time'];?>">
                                                    <input type="hidden" value="<?php echo $week5['id']; ?>" id="week5_id"/>
                                                    <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                            </span>
                                                </div>
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_thursday_start_time"></span>
                                            </td>
                                            <td>
                                                <div class="input-group form-group clockpicker">
                                                    <input type="text" id="thursday_end_time" class="form-control" placeholder="Select Time"
                                                           value="<?php echo $week5['closing_time'];?>">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>
                                                </div>
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_thursday_end_time"></span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                Friday
                                            </td>
                                            <td>
                                                <div class="input-group form-group clockpicker">
                                                    <input type="text" id="friday_start_time" class="form-control" placeholder="Select Time"
                                                           value="<?php echo $week6['opening_time'];?>">
                                                    <input type="hidden" value="<?php echo $week6['id']; ?>" id="week6_id"/>
                                                    <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                            </span>
                                                </div>
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_friday_start_time"></span>
                                            </td>
                                            <td>
                                                <div class="input-group form-group clockpicker">
                                                    <input type="text" id="friday_end_time" class="form-control" placeholder="Select Time"
                                                           value="<?php echo $week6['closing_time'];?>">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>
                                                </div>
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_friday_end_time"></span>

                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                Saturday
                                            </td>
                                            <td>
                                                <div class="input-group form-group clockpicker">
                                                    <input type="text" id="saturday_start_time" class="form-control" placeholder="Select Time"
                                                           value="<?php echo $week7['opening_time'];?>">
                                                    <input type="hidden" value="<?php echo $week7['id']; ?>" id="week7_id"/>
                                                    <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                            </span>
                                                </div>
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_saturday_start_time"></span>
                                            </td>
                                            <td>
                                                <div class="input-group form-group clockpicker">
                                                    <input type="text" id="saturday_end_time" class="form-control" placeholder="Select Time"
                                                           value="<?php echo $week7['closing_time'];?>">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>
                                                </div>
                                                <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="error_saturday_end_time"></span>
                                            </td>
                                        </tr>

                                        </tbody>

                                    </table>



                                    <div class="form-actions">
                                        <div onclick="add_timing('<?=$restaurant_id?>','<?=$_SERVER['REQUEST_URI']?>')" class="btn btn-primary btn-lg">
                                            <i class="fa fa-save"></i>
                                            Submit
                                        </div>
                                    </div>

                                </form>
                                <!--   RESTAURANT DELIVERY ADDRESS AND FEES-->

                            </div>

                        </div>

                    </div>
                </article>

            </div>


            <div class="row">

                <!-- col -->
                <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                    <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-truck"></i> Add Delivery Address</h1>
                </div>

            </div>

            <div class="row">
                <!-- NEW WIDGET START -->
                <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                    <div style="display: none" id="add-delivery-address" >
                        <div  class="jarviswidget jarviswidget-color-darken" id="wid-id-3" data-widget-editbutton="false">
                            <div>
                                <!-- widget edit box -->
                                <div class="jarviswidget-editbox">
                                    <!-- This area used as dropdown edit box -->

                                </div>
                                <!-- end widget edit box -->
                                <style>
                                    .map_canvas {
                                        width: 500px;
                                        height: 300px;
                                        margin: 10px 20px 10px 0;
                                    }

                                </style>
                                <!-- widget content -->
                                <form method="post">
                                    <fieldset>

                                        <div class="form-group">
                                            <label>Delivery Address</label>
                                            <input class="form-control" id="area_en" name="area_en" placeholder="Enter Address" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="area_en_error"></span>
                                        </div>

                                        <div class="form-group">
                                            <label dir="rtl">כתובת</label>
                                            <input style="direction:RTL;" class="form-control" id="area_he" name="area_he"  type="text">
                                            <span style="direction:RTL;font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="area_he_error"></span>
                                        </div>
                                        <div style="display:none" class="row form-group">
                                            <div id="map" class="map_canvas"></div>
                                        </div>
                                        <input type="hidden" id="lat" name="lat" value="">
                                        <input type="hidden" id="lng" name="lng" value="">

                                        <div class="form-group">
                                            <label>Fees</label>
                                            <input class="form-control" id="fee" name="fee" placeholder="Enter Fees" type="text">
                                            <span style="font-size: 14px; color: red; width: 100%;text-align: left; padding: 9px;text-transform: none;" id="fee_error"></span>
                                        </div>
                                    </fieldset>
                                    <div class="form-actions">
                                        <div onclick="add_delivery_address('<?=$restaurant_id?>','<?=$_SERVER['REQUEST_URI']?>')" class="btn btn-primary btn-lg">
                                            <i class="fa fa-save"></i>
                                            Submit
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div onclick="show_delivery_address()" class="btn btn-primary btn-lg" style="margin-top: 2%;">
                        <i class="fa fa-plus"></i>
                        Add Delivery Address
                    </div><br>

                </article>

            </div><br>
            <div class="row">
                <!-- NEW WIDGET START -->
                <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-1" data-widget-editbutton="false">

                        <header>
                            <span class="widget-icon"> <i class="fa fa-cutlery"></i> </span>
                            <h2>Delivery Address </h2>
                        </header>
                        <!-- widget div-->
                        <div>
                            <!-- widget edit box -->
                            <div class="jarviswidget-editbox">
                                <!-- This area used as dropdown edit box -->
                            </div>
                            <div class="widget-body no-padding">
                                <table class="table table-striped table-bordered table-hover" width="100%">
                                    <thead>
                                    <tr>
                                        <th data-class="expand"><i class="fa fa-fw fa-address-card  text-muted hidden-md hidden-sm hidden-xs"></i> Address</th>
                                        <th data-hide="expand"><i class="fa-fw fa fa-address-card  text-muted hidden-md hidden-sm hidden-xs"></i> כתובת </th>
                                        <th data-hide="phone"><i class="fa-fw fa fa-money  text-muted hidden-md hidden-sm hidden-xs"></i> Fees </th>
                                        <th data-hide="phone"><i class="fa-fw fa   text-muted hidden-md hidden-sm hidden-xs"></i> Edit </th>
                                        <th data-hide="phone"><i class="fa-fw fa   text-muted hidden-md hidden-sm hidden-xs"></i> Delete </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php

                                    foreach($delivery_address as $delivery) {

                                        ?>
                                        <tr>

                                            <td><?=$delivery['area_en']?></td>
                                            <td><?=$delivery['area_he']?></td>
                                            <td><?=$delivery['fee']?></td>

                                            <td><a href="edit-address.php?id=<?=$delivery['id']?>"><button class="btn btn-labeled btn-primary bg-color-blueDark txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-edit"></i> Edit </button></a></td>
                                            <td><a onclick="delete_delivery_address('<?=$delivery['id']?>','<?=$_SERVER['REQUEST_URI']?>')"><button class="btn btn-labeled btn-danger  txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-trash-o"></i> Delete </button></a></td>
                                        </tr>
                                    <?php  } ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
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
