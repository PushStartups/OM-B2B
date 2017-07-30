<?php
session_start();
require_once 'inc/initDb.php';
require_once 'inc/functions.php';
require_once 'inc/constants.php';
checkAdminSession();
DB::query("set names utf8");
DB::useDB(B2B_RESTAURANTS);

$rolee = $_SESSION['b2b_admin_role'];
?>

<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title> OrderApp-Admin Restaurants</title>
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- #CSS Links -->
    <!-- Basic Styles -->
    <link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="css/font-awesome.min.css">

    <!-- SmartAdmin Styles : Caution! DO NOT change the order -->
    <link rel="stylesheet" type="text/css" media="screen" href="css/orderappadmin-production-plugins.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="css/orderappadmin-production.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="css/orderappadmin-skins.min.css">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <!-- SmartAdmin RTL Support -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" type="text/css" media="screen" href="css/orderappadmin-rtl.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="css/orderappadmin.css">
    <link rel="stylesheet" type="text/css" media="screen" href="css/clockpicker.css">
    <link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap-multiselect.css">
    <link rel="stylesheet" type="text/css" media="screen" href="css/sweetalert.css">
    <!-- We recommend you use "your_style.css" to override SmartAdmin
         specific styles this will also ensure you retrain your customization with each SmartAdmin update.
    <link rel="stylesheet" type="text/css" media="screen" href="css/your_style.css"> -->

    <!-- Demo purpose only: goes with demo.js, you can delete this css when designing your own WebApp -->
    <!--<link rel="stylesheet" type="text/css" media="screen" href="css/demo.min.css">-->


    <!-- #FAVICONS -->
    <link rel="shortcut icon" href="img/favicon/favicon.png" type="image/x-icon">
    <link rel="icon" href="img/favicon/favicon.png" type="image/x-icon">

    <!-- #GOOGLE FONT -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

    <!-- #APP SCREEN / ICONS -->
    <!-- Specifying a Webpage Icon for Web Clip
         Ref: https://developer.apple.com/library/ios/documentation/AppleApplications/Reference/SafariWebContent/ConfiguringWebApplications/ConfiguringWebApplications.html -->
    <link rel="apple-touch-icon" href="img/splash/sptouch-icon-iphone.png">
    <link rel="apple-touch-icon" sizes="76x76" href="img/splash/touch-icon-ipad.png">
    <link rel="apple-touch-icon" sizes="120x120" href="img/splash/touch-icon-iphone-retina.png">
    <link rel="apple-touch-icon" sizes="152x152" href="img/splash/touch-icon-ipad-retina.png">

    <!-- iOS web-app metas : hides Safari UI Components and Changes Status Bar Appearance -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">

    <!-- Startup image for web apps -->
    <link rel="apple-touch-startup-image" href="img/splash/ipad-landscape.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)">
    <link rel="apple-touch-startup-image" href="img/splash/ipad-portrait.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)">
    <link rel="apple-touch-startup-image" href="img/splash/iphone.png" media="screen and (max-device-width: 320px)">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="js/bootstrap/bootstrap.min.js"></script>
  
</head>
<body class="">
<img id="loader" style="z-index: 99999;" class="loader-css" src="<?=WEB_PATH?>/en/img/loader.gif" >
<div id="Loader_bg" style="display:none ; z-index: 9999; width: 100%; height: 100%; position: absolute; top: 0; left: 0;right:  0; bottom: 0; background-color: rgba(255,233,206,0.9);"></div>


<!-- #HEADER -->
<header id="header">
    <div id="logo-group">

        <!-- PLACE YOUR LOGO HERE -->
        <span id="logo"> <img src="img/logo.png" alt="SmartAdmin"> </span>
        <!-- END LOGO PLACEHOLDER -->

        <!-- AJAX-DROPDOWN : control this dropdown height, look and feel from the LESS variable file -->
        <div class="ajax-dropdown">

            <!-- the ID links are fetched via AJAX to the ajax container "ajax-notifications" -->
            <div class="btn-group btn-group-justified" data-toggle="buttons">
                <label class="btn btn-default">
                    <input type="radio" name="activity" id="ajax/notify/mail.html">
                    Msgs (14) </label>
                <label class="btn btn-default">
                    <input type="radio" name="activity" id="ajax/notify/notifications.html">
                    notify (3) </label>
                <label class="btn btn-default">
                    <input type="radio" name="activity" id="ajax/notify/tasks.html">
                    Tasks (4) </label>
            </div>

            <!-- notification content -->
            <div class="ajax-notifications custom-scroll">

                <div class="alert alert-transparent">
                    <h4>Click a button to show messages here</h4>
                    This blank page message helps protect your privacy, or you can show the first message here automatically.
                </div>

                <i class="fa fa-lock fa-4x fa-border"></i>

            </div>
            <!-- end notification content -->

            <!-- footer: refresh area -->
				<span> Last updated on: 12/12/2013 9:43AM
						<button type="button" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Loading..." class="btn btn-xs btn-default pull-right">
							<i class="fa fa-refresh"></i>
						</button>
                </span>
            <!-- end footer -->

        </div>
        <!-- END AJAX-DROPDOWN -->
    </div>

</header>
<!-- END HEADER -->
<!-- #NAVIGATION -->
<aside id="left-panel">
    <nav>
        <ul>
            <!--			<li>-->
            <!--				<a href="dashboard-social.html" title="Dashboard"><i class="fa fa-lg fa-fw fa-home"></i> <span class="menu-item-parent">Dashboard</span></a>-->
            <!--			</li>-->
            <li>
                <a href="#" title="Restaurant"><i class="fa fa-lg fa-fw fa-cutlery"></i> <span class="menu-item-parent">B2B Restaurants</span></a>
                <ul>
                    <?php  $city = getAllCities();
                    foreach ($city as $cities)
                    {
                        DB::query("select * from restaurants where city_id = '".$cities['id']."'");
                        $count = DB::count();
                        ?>
                        <li>
                            <a href="index.php?id=<?=$cities['id']?>" title=<?=$cities['name_en']?>><span class="menu-item-parent"><?=$cities['name_en']?>&nbsp;&nbsp;&nbsp;(<?=$count?>)</span></a>
                        </li>
                        <?php
                    } ?>
                </ul>
            </li>
<!--            <li>-->
<!--                <a href="tags.php" title="Tags"><i class="fa fa-lg fa-fw fa-tags"></i> <span class="menu-item-parent">Tags</span></a>-->
<!--            </li>-->
<!--            <li>-->
<!--                <a href="orders.php" title="Orders"><i class="fa fa-lg fa-fw fa-shopping-cart"></i> <span class="menu-item-parent">Orders</span></a>-->
<!--            </li>-->
            <li>
                <a href="companies.php" title="Companies"><i class="fa fa-lg fa-fw fa-briefcase"></i> <span class="menu-item-parent">B2B Company</span></a>
            </li>
            <li>
                <a href="manage-users.php" title="Manage Users"><i class="fa fa-lg fa-fw fa-user"></i> <span class="menu-item-parent">B2B Users</span></a>
            </li>
            <li>
                <a href="b2b-orders.php" title="Orders"><i class="fa fa-lg fa-fw fa-shopping-cart"></i> <span class="menu-item-parent">B2B Orders</span></a>
            </li>
            <li>
                <a href="b2b-ledger.php" title="B2B Ledger"><i class="fa fa-lg fa-fw fa-files-o"></i> <span class="menu-item-parent">B2B Ledger </span></a>
            </li>
            <li>
                <a href="kashrut.php" title="Kashrut"><i class="fa fa-lg fa-fw fa-plus"></i> <span class="menu-item-parent">Kashrut</span></a>
            </li>
            <li>
                <a href="b2b-rest-discounts.php" title="Discounts"><i class="fa fa-lg fa-fw fa-tags"></i> <span class="menu-item-parent">B2B Rest Disc</span></a>
            </li>
            <li>
                <a href="stock-reports.php" title="Stock Invoice Taxing"><i class="fa fa-lg fa-fw fa-files-o"></i> <span class="menu-item-parent">Stock Invoice Taxes </span></a>
            </li>


            <?php if ($rolee == 1) {?>
            <li>
                <a href="add-new-admin.php" title="Add Admin"><i class="fa fa-lg fa-fw fa-user-secret"></i> <span class="menu-item-parent">Add New Admin </span></a>
            </li>
            <?php } ?>

            <li>
                <a href="logout.php" title="Restaurant"><i class="fa fa-lg fa-fw fa-sign-out"></i> <span class="menu-item-parent">Sign Out</span></a>
            </li>
        </ul>
    </nav>


		<span class="minifyme" data-action="minifyMenu">
				<i class="fa fa-arrow-circle-left hit"></i>
        </span>

</aside>