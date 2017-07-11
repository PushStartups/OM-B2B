<?php
session_start();
require_once 'inc/initDb.php';
require_once 'inc/functions.php';
require_once 'inc/constants.php';
checkAdminSession();
DB::query("set names utf8");
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
	<link rel="stylesheet" type="text/css" media="screen" href="css/orderappadmin-rtl.min.css">
	<link rel="stylesheet" type="text/css" media="screen" href="css/orderappadmin.css">

	<!-- We recommend you use "your_style.css" to override SmartAdmin
         specific styles this will also ensure you retrain your customization with each SmartAdmin update.
    <link rel="stylesheet" type="text/css" media="screen" href="css/your_style.css"> -->

	<!-- Demo purpose only: goes with demo.js, you can delete this css when designing your own WebApp -->
	<!--<link rel="stylesheet" type="text/css" media="screen" href="css/demo.min.css">-->


	<!-- #FAVICONS -->
	<link rel="shortcut icon" href="img/favicon/favicon.png" type="image/x-icon">
	<link rel="icon" href="img/favicon/favicon.png" type="image/x-icon">

	<!-- #GOOGLE FONT -->
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

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
						</button> </span>
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
			<li>
				<a href="dashboard-social.html" title="Dashboard"><i class="fa fa-lg fa-fw fa-home"></i> <span class="menu-item-parent">Dashboard</span></a>
			</li>
			<li class="active">
				<a href="restaurant.php" title="Restaurant"><i class="fa fa-lg fa-fw fa-cutlery"></i> <span class="menu-item-parent">Restaurant</span></a>
			</li>
			<li>
				<a href="logout.php" title="Restaurant"><i class="fa fa-lg fa-fw fa-sign-out"></i> <span class="menu-item-parent">Sign Out</span></a>
			</li>
		</ul>
	</nav>


		<span class="minifyme" data-action="minifyMenu">
				<i class="fa fa-arrow-circle-left hit"></i>
			</span>

</aside>
<!-- END NAVIGATION -->

<!-- MAIN PANEL -->
<div id="main" role="main">


	<!-- MAIN CONTENT -->
	<div id="content">

		<!-- row -->
		<div class="row">

			<!-- col -->
			<div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
				<h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-cutlery"></i> Restaurants</h1>
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
					<li class="sparks-info">
						<?php $count1 = getRestaurantsCountByCity(1);  ?>
						<h5> Beit Shemesh <span class="txt-color-purple">&nbsp;<?=$count1?></span></h5>
					</li>
					<li class="sparks-info">
						<?php $count2 = getRestaurantsCountByCity(2);  ?>
						<h5> Modiin <span class="txt-color-greenDark">&nbsp;<?=$count2?></span></h5>
					</li>
				</ul>
				<!-- end sparks -->
			</div>
			<!-- end col -->

		</div>
		<!-- end row -->

		<!--
        The ID "widget-grid" will start to initialize all widgets below
        You do not need to use widgets if you dont want to. Simply remove
        the <section></section> and you can use wells or panels instead  -->

		<!-- widget grid -->
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
									<!-- This area used as dropdown edit box -->

								</div>
								<!-- end widget edit box -->

								<!-- widget content -->
								<div class="widget-body no-padding">

									<table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
										<thead>
										<tr>
											<th data-hide="phone">ID</th>
											<th data-class="expand"><i class="fa fa-fw fa-user text-muted hidden-md hidden-sm hidden-xs"></i> Logo</th>
											<th data-hide="phone"><i class="fa-fw fa fa-cutlery text-muted hidden-md hidden-sm hidden-xs"></i> Name EN</th>
											<th data-hide="phone"><i class="fa-fw fa fa-cutlery text-muted hidden-md hidden-sm hidden-xs"></i> Name HE</th>
											<th data-hide="phone,tablet">City</th>
											<th data-hide="phone,tablet">Hide/Show</th>
											<th data-hide="phone,tablet">Rank</th>
											<th data-hide="phone,tablet"><i class="fa fa-fw fa-calendar txt-color-blue hidden-md hidden-sm hidden-xs"></i> Action</th>
										</tr>
										</thead>
										<tbody>
										<?php
										//GETTING ALL RESTAURANTS
										$restaurant = getAllRestaurants();
										foreach($restaurant as $restaurants){ $i = 0;

											?>
											<tr>
												<td><?=$restaurants['id']?></td>
												<td><img class="logo-table" src="<?=WEB_PATH.$restaurants['logo'] ?>"></td>
												<td><?=$restaurants['name_en']?></td>
												<td><?=$restaurants['name_he']?></td>
												<td><?=$restaurants['city_name']?></td>
												<td>
													<div class="onoffswitch">
														<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="<?=$restaurants['id']?>" <?php if($restaurants['hide'] == '0'){ ?> checked <?php } ?>>
														<label class="onoffswitch-label" for="<?=$restaurants['id']?>">
															<span class="onoffswitch-inner"></span>
															<span class="onoffswitch-switch"></span>
														</label>
													</div>
												</td>
												<td>
													<div class="tel-holder">
														<input class="form-control" id="rank<?=$restaurants['id']?>" value="<?=$restaurants['sort']?>" type="tel">
														<button onclick="change_rank('<?=$restaurants['id']?>')" class="btn btn-labeled btn-primary add"><i class="fa fa-fw fa-save"></i> Save </button>
													</div>
												</td>
												<td><button class="btn btn-labeled btn-primary bg-color-blueDark txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-edit"></i> Edit </button></td>

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
<div class="page-footer">
	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<span class="txt-color-white">OrderApp</span>
		</div>
	</div>
</div>
<!-- END PAGE FOOTER -->

<!-- SHORTCUT AREA : With large tiles (activated via clicking user name tag)
Note: These tiles are completely responsive,
you can add as many as you like
-->

<!-- END SHORTCUT AREA -->

<!--================================================== -->

<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)-->
<script data-pace-options='{ "restartOnRequestAfter": true }' src="js/plugin/pace/pace.min.js"></script>

<!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script>
	if (!window.jQuery) {
		document.write('<script src="js/libs/jquery-2.1.1.min.js"><\/script>');
	}
</script>

<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script>
	if (!window.jQuery.ui) {
		document.write('<script src="js/libs/jquery-ui-1.10.3.min.js"><\/script>');
	}
</script>

<!-- IMPORTANT: APP CONFIG -->
<script src="js/app.config.js"></script>

<!-- JS TOUCH : include this plugin for mobile drag / drop touch events-->
<script src="js/plugin/jquery-touch/jquery.ui.touch-punch.min.js"></script>

<!-- BOOTSTRAP JS -->
<script src="js/bootstrap/bootstrap.min.js"></script>

<!-- CUSTOM NOTIFICATION -->
<script src="js/notification/SmartNotification.min.js"></script>

<!-- JARVIS WIDGETS -->
<script src="js/smartwidgets/jarvis.widget.min.js"></script>

<!-- EASY PIE CHARTS -->
<script src="js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js"></script>

<!-- SPARKLINES -->
<script src="js/plugin/sparkline/jquery.sparkline.min.js"></script>

<!-- JQUERY VALIDATE -->
<script src="js/plugin/jquery-validate/jquery.validate.min.js"></script>

<!-- JQUERY MASKED INPUT -->
<script src="js/plugin/masked-input/jquery.maskedinput.min.js"></script>

<!-- JQUERY SELECT2 INPUT -->
<script src="js/plugin/select2/select2.min.js"></script>

<!-- JQUERY UI + Bootstrap Slider -->
<script src="js/plugin/bootstrap-slider/bootstrap-slider.min.js"></script>

<!-- browser msie issue fix -->
<script src="js/plugin/msie-fix/jquery.mb.browser.min.js"></script>

<!-- FastClick: For mobile devices -->
<script src="js/plugin/fastclick/fastclick.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<!--[if IE 8]>

<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>

<![endif]-->

<!-- Demo purpose only -->
<!--<script src="js/demo.min.js"></script>-->

<!-- MAIN APP JS FILE -->
<script src="js/app.min.js"></script>

<!-- ENHANCEMENT PLUGINS : NOT A REQUIREMENT -->
<!-- Voice command : plugin -->
<script src="js/speech/voicecommand.min.js"></script>

<!-- SmartChat UI : plugin -->
<script src="js/smart-chat-ui/smart.chat.ui.min.js"></script>
<script src="js/smart-chat-ui/smart.chat.manager.min.js"></script>

<!-- PAGE RELATED PLUGIN(S) -->
<script src="js/plugin/datatables/jquery.dataTables.min.js"></script>
<script src="js/plugin/datatables/dataTables.colVis.min.js"></script>
<script src="js/plugin/datatables/dataTables.tableTools.min.js"></script>
<script src="js/plugin/datatables/dataTables.bootstrap.min.js"></script>
<script src="js/plugin/datatable-responsive/datatables.responsive.min.js"></script>
<script src="js/custom/restaurants.js"></script>
<script type="text/javascript">
	hideLoading();
</script>
<script type="text/javascript">

	// DO NOT REMOVE : GLOBAL FUNCTIONS!

	$(document).ready(function() {

		pageSetUp();

		/* // DOM Position key index //

		 l - Length changing (dropdown)
		 f - Filtering input (search)
		 t - The Table! (datatable)
		 i - Information (records)
		 p - Pagination (paging)
		 r - pRocessing
		 < and > - div elements
		 <"#id" and > - div with an id
		 <"class" and > - div with a class
		 <"#id.class" and > - div with an id and class

		 Also see: http://legacy.datatables.net/usage/features
		 */

		/* BASIC ;*/
		var responsiveHelper_dt_basic = undefined;
		var responsiveHelper_datatable_fixed_column = undefined;
		var responsiveHelper_datatable_col_reorder = undefined;
		var responsiveHelper_datatable_tabletools = undefined;

		var breakpointDefinition = {
			tablet : 1024,
			phone : 480
		};

		$('#dt_basic').dataTable({
			"sDom": "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'l>r>"+
			"t"+
			"<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
			"autoWidth" : true,
			"oLanguage": {
				"sSearch": '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>'
			},
			"preDrawCallback" : function() {
				// Initialize the responsive datatables helper once.
				if (!responsiveHelper_dt_basic) {
					responsiveHelper_dt_basic = new ResponsiveDatatablesHelper($('#dt_basic'), breakpointDefinition);
				}
			},
			"rowCallback" : function(nRow) {
				responsiveHelper_dt_basic.createExpandIcon(nRow);
			},
			"drawCallback" : function(oSettings) {
				responsiveHelper_dt_basic.respond();
			}
		});

		/* END BASIC */

		/* COLUMN FILTER  */
		var otable = $('#datatable_fixed_column').DataTable({
			//"bFilter": false,
			//"bInfo": false,
			//"bLengthChange": false
			//"bAutoWidth": false,
			//"bPaginate": false,
			//"bStateSave": true // saves sort state using localStorage
			"sDom": "<'dt-toolbar'<'col-xs-12 col-sm-6 hidden-xs'f><'col-sm-6 col-xs-12 hidden-xs'<'toolbar'>>r>"+
			"t"+
			"<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
			"autoWidth" : true,
			"oLanguage": {
				"sSearch": '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>'
			},
			"preDrawCallback" : function() {
				// Initialize the responsive datatables helper once.
				if (!responsiveHelper_datatable_fixed_column) {
					responsiveHelper_datatable_fixed_column = new ResponsiveDatatablesHelper($('#datatable_fixed_column'), breakpointDefinition);
				}
			},
			"rowCallback" : function(nRow) {
				responsiveHelper_datatable_fixed_column.createExpandIcon(nRow);
			},
			"drawCallback" : function(oSettings) {
				responsiveHelper_datatable_fixed_column.respond();
			}

		});

		// custom toolbar
		$("div.toolbar").html('<div class="text-right"><img src="img/logo.png" alt="SmartAdmin" style="width: 111px; margin-top: 3px; margin-right: 10px;"></div>');

		// Apply the filter
		$("#datatable_fixed_column thead th input[type=text]").on( 'keyup change', function () {

			otable
				.column( $(this).parent().index()+':visible' )
				.search( this.value )
				.draw();

		} );
		/* END COLUMN FILTER */

		/* COLUMN SHOW - HIDE */
		$('#datatable_col_reorder').dataTable({
			"sDom": "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-6 hidden-xs'C>r>"+
			"t"+
			"<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-sm-6 col-xs-12'p>>",
			"autoWidth" : true,
			"oLanguage": {
				"sSearch": '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>'
			},
			"preDrawCallback" : function() {
				// Initialize the responsive datatables helper once.
				if (!responsiveHelper_datatable_col_reorder) {
					responsiveHelper_datatable_col_reorder = new ResponsiveDatatablesHelper($('#datatable_col_reorder'), breakpointDefinition);
				}
			},
			"rowCallback" : function(nRow) {
				responsiveHelper_datatable_col_reorder.createExpandIcon(nRow);
			},
			"drawCallback" : function(oSettings) {
				responsiveHelper_datatable_col_reorder.respond();
			}
		});

		/* END COLUMN SHOW - HIDE */

		/* TABLETOOLS */
		$('#datatable_tabletools').dataTable({

			// Tabletools options:
			//   https://datatables.net/extensions/tabletools/button_options
			"sDom": "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-6 hidden-xs'T>r>"+
			"t"+
			"<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-sm-6 col-xs-12'p>>",
			"oLanguage": {
				"sSearch": '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>'
			},
			"oTableTools": {
				"aButtons": [
					"copy",
					"csv",
					"xls",
					{
						"sExtends": "pdf",
						"sTitle": "SmartAdmin_PDF",
						"sPdfMessage": "SmartAdmin PDF Export",
						"sPdfSize": "letter"
					},
					{
						"sExtends": "print",
						"sMessage": "Generated by SmartAdmin <i>(press Esc to close)</i>"
					}
				],
				"sSwfPath": "js/plugin/datatables/swf/copy_csv_xls_pdf.swf"
			},
			"autoWidth" : true,
			"preDrawCallback" : function() {
				// Initialize the responsive datatables helper once.
				if (!responsiveHelper_datatable_tabletools) {
					responsiveHelper_datatable_tabletools = new ResponsiveDatatablesHelper($('#datatable_tabletools'), breakpointDefinition);
				}
			},
			"rowCallback" : function(nRow) {
				responsiveHelper_datatable_tabletools.createExpandIcon(nRow);
			},
			"drawCallback" : function(oSettings) {
				responsiveHelper_datatable_tabletools.respond();
			}
		});

		/* END TABLETOOLS */

	})

</script>

<!-- Your GOOGLE ANALYTICS CODE Below -->
<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', 'UA-XXXXXXXX-X']);
	_gaq.push(['_trackPageview']);

	(function() {
		var ga = document.createElement('script');
		ga.type = 'text/javascript';
		ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0];
		s.parentNode.insertBefore(ga, s);
	})();
</script>

</body>

</html>