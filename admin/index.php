<?php

include "header.php";

$rolee = $_SESSION['b2b_admin_role'];

if(isset($_GET['id'])){
    $city_id = $_GET['id'];
}
else{
    $city_id = 1;
}
?>
<style>
    .bg-color-orange {
        background-color: #FF6600 !important;
    }
    .btn-purple {
        color: #fff;
        background-color: #a949be;
        border-color: #8f2ca5;
    }
    .bg-color-pink {
        background-color: #d75b85 !important;
    }
</style>

<!-- END NAVIGATION -->

<!-- MAIN PANEL -->
<div id="main" role="main">


    <!-- MAIN CONTENT -->
    <div id="content">

        <!-- row -->
        <div class="row">

            <!-- col -->
            <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-cutlery"></i>B2B Restaurants</h1>
            </div>
            <!-- end col -->

            <!-- right side of the page with the sparkline graphs -->
            <!-- col -->
            <div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">
                <!-- sparks -->
                <ul id="sparks">
                    <li class="sparks-info">
                        <?php $count = getTotalRestaurants();  ?>
                        <h5> Total Restaurants <span class="txt-color-blue"><?=$count?></span></h5>
                    </li>

                </ul>
                <!-- end sparks -->
            </div><br>


        </div>
        <br>
        <?php if ($rolee == 1) {?>
            <div align="center">
                <a style="text-decoration: none" href="add-new-restaurant.php"><div class="btn btn-purple btn-lg">
                        <i class="fa fa-plus"></i>
                        Add B2B Restaurants
                    </div></a>
            </div><br><br>
        <?php } ?>

        <section id="widget-grid" class="">
            <!-- row -->
            <div class="row">
                <!-- NEW WIDGET START -->
                <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                    <div class="jarviswidget jarviswidget-color-darken" id="wid-id-0" data-widget-editbutton="false">

                        <header>
                            <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                            <h2>Restaurants</h2>
                        </header>

                        <!-- widget div-->
                        <div>

                            <!-- widget edit box -->
                            <div class="jarviswidget-editbox">

                            </div>

                            <div class="widget-body no-padding">

                                <table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
                                    <thead>
                                    <tr>
                                        <th data-hide="phone">ID</th>
                                        <th data-class="expand"><i class="fa fa-fw fa-user text-muted hidden-md hidden-sm hidden-xs"></i> Logo</th>
                                        <th data-hide="phone"><i class="fa-fw fa fa-cutlery text-muted hidden-md hidden-sm hidden-xs"></i> Name EN</th>
                                        <th data-hide="phone,tablet">City</th>


                                        <?php if ($rolee == 1) {?>
                                        <th data-hide="phone,tablet">Hide/Show</th>
                                        <?php } ?>

                                        <th data-hide="phone,tablet">Rank</th>

                                            <th data-hide="phone,tablet">Categories</th>
                                            <th data-hide="phone,tablet">Timings & Delivery Address</th>
                                            <th data-hide="phone,tablet">Tags</th>
                                            <th data-hide="phone,tablet">Kashruts</th>

                                        <?php if ($rolee == 1) {?>
                                            <th data-hide="phone,tablet"><i class="fa fa-fw fa-edit txt-color-blue hidden-md hidden-sm hidden-xs"></i> Action</th>
                                        <?php } ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    //GETTING ALL RESTAURANTS
                                    $restaurant = getAllRestaurantsByCity($city_id);
                                    foreach($restaurant as $restaurants) { $i = 0;

                                        ?>
                                        <tr>
                                            <td><?=$restaurants['id']?></td>
                                            <td><img class="logo-table" src="<?=WEB_PATH.$restaurants['logo'] ?>"></td>
                                            <td><?=$restaurants['name_en']?></td>
                                            <td><?=$restaurants['city_name']?></td>


                                            <?php if ($rolee == 1) {?>
                                            <td>
                                                <div class="onoffswitch">
                                                    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="<?=$restaurants['id']?>" <?php if($restaurants['hide'] == '0'){ ?> checked <?php } ?>>
                                                    <label class="onoffswitch-label" for="<?=$restaurants['id']?>">
                                                        <span class="onoffswitch-inner"></span>
                                                        <span class="onoffswitch-switch"></span>
                                                    </label>
                                                </div>
                                            </td>
                                            <?php }?>


                                            <?php if ($rolee == 1) {?>
                                            <td>
                                                <div class="tel-holder">
                                                    <input class="form-control" id="rank<?=$restaurants['id']?>" value="<?=$restaurants['sort']?>" type="tel">
                                                    <button onclick="change_rank('<?=$restaurants['id']?>','<?=$city_id?>')" class="btn btn-labeled btn-primary add"><i class="fa fa-fw fa-save"></i> Save </button>
                                                </div>
                                            </td>
                                            <?php } else if($rolee == 0) { ?>
                                                <td>
                                                    <?=$restaurants['sort']?>
                                                </td>
                                            <?php }?>

                                                <td><a style="text-decoration: none" href="add-new-category.php?id=<?=$restaurants['id']?>"><button class="btn btn-labeled btn-success  txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-plus"></i> Categories </button></a></td>
                                                <td><a style="text-decoration: none" href="add-restaurant-timing.php?id=<?=$restaurants['id']?>"><button class="btn btn-labeled bg-color-pink  txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-plus"></i>  Timings & Delivery Address </button></a></td>
                                                <td><a style="text-decoration: none" href="add-tags.php?id=<?=$restaurants['id']?>"><button class="btn btn-labeled bg-color-orange txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-plus"></i>  Tags </button></a></td>

                                                <td><a style="text-decoration: none" href="add-rest-kashrut.php?id=<?=$restaurants['id']?>"><button class="btn btn-labeled bg-color-yellow txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-plus"></i>  Kashrut </button></a></td>

                                            <?php if ($rolee == 1) {?>
                                                <td><a href="edit-restaurant.php?id=<?=$restaurants['id']?>"><button class="btn btn-labeled btn-primary bg-color-blueDark txt-color-white add" style="border-color: #4c4f53;" ><i class="fa fa-fw fa-edit"></i> Edit </button></a></td>
                                            <?php } ?>
                                        </tr>
                                    <?php  } ?>

                                    </tbody>
                                </table>


                            </div>
                            <!-- end widget content -->
                        </div>
                        <!-- end widget div -->
                    </div>
                </article>
                <!-- WIDGET END -->
            </div>

            <!-- end row -->

        </section>

        <!-- end widget grid -->

    </div>
    <!-- END MAIN CONTENT -->

</div>
<!-- END MAIN PANEL -->

<!-- PAGE FOOTER -->
<?php
include "footer.php";
?>

