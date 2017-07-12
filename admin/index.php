<?php

include "header.php";
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
		<div align="center">
			<a style="text-decoration: none" href="add-new-restaurant.php"><div class="btn btn-purple btn-lg">
					<i class="fa fa-plus"></i>
					Add B2B Restaurants
				</div></a>
		</div><br><br>

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
											<th data-hide="phone,tablet">Hide/Show</th>
											<th data-hide="phone,tablet">Rank</th>
										
											<th data-hide="phone,tablet">Add Categories</th>
											<th data-hide="phone,tablet">Add Timings & Delivery Address</th>
											<th data-hide="phone,tablet">Add Tags</th>
											<th data-hide="phone,tablet"><i class="fa fa-fw fa-edit txt-color-blue hidden-md hidden-sm hidden-xs"></i> Action</th>
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
														<button onclick="change_rank('<?=$restaurants['id']?>','<?=$city_id?>')" class="btn btn-labeled btn-primary add"><i class="fa fa-fw fa-save"></i> Save </button>
													</div>
												</td>


												<td><a style="text-decoration: none" href="add-new-category.php?id=<?=$restaurants['id']?>"><button class="btn btn-labeled btn-success  txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-plus"></i> Add Categories </button></a></td>
												<td><a style="text-decoration: none" href="add-restaurant-timing.php?id=<?=$restaurants['id']?>"><button class="btn btn-labeled bg-color-pink  txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-plus"></i> Add Timings & Delivery Address </button></a></td>
												<td><a style="text-decoration: none" href="add-tags.php?id=<?=$restaurants['id']?>"><button class="btn btn-labeled bg-color-orange txt-color-white add" style="border-color: #4c4f53;"><i class="fa fa-fw fa-plus"></i> Add Tags </button></a></td>

												<td><a href="edit-restaurant.php?id=<?=$restaurants['id']?>"><button class="btn btn-labeled btn-primary bg-color-blueDark txt-color-white add" style="border-color: #4c4f53;" ><i class="fa fa-fw fa-edit"></i> Edit </button></a></td>

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

