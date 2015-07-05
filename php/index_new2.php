<?php
include ('_dbconntect.php');
include ('_header.php');
include('_digitalnz_any.php');
?>


<div class="jumbotron" style="background-image:url(<?= $digitalnz_image_url; ?>);background-size:cover;background-position:center center;">
	<div class="container">
		<div class="col-xs-8 col-sm-5 col-md-3 col-xs-offset-3 col-sm-offset-6">
			<div class="location-name-holder-home">
				<h1><img src="/static/img/tourismBizKitLogoInv.png" class="img-responsive" alt="Gate 5B Tourism BizKit"></h1>
			</div>
		</div>
		<div class="col-xs-3">
		</div>
	</div>
</div>

<div class="container">
		<div class="row">
		<div class="col-sm-4 col-sm-offset-2">
			<h2 class="alt" style="color:#77a685;">Your Tourism Business <span style="font-size:150%;display:block;">Gateway</span></h2>
			<p>Thinking about starting a small New Zealand tourism business?</p>
				<p>This tool helps you:
					<ul>
						<li>Explore your idea and tourist activity in your region</li>
						<li>Understand local cultural insights and perspectives</li>
					</ul>
				</p>

				<p>Where are the tourists coming from?  Where are they going? What do they do? What areas are growing?</p>

			<h4 class="text-center" style="margin-top:50px; color:#b15470;">Select a region on the map where you want to launch your business <span class="hidden-xs">&rarr;</span><span class="visible-xs-inline">&darr;</span></h4>

		</div>

		<div class="col-sm-6">
			<div id="map-canvas" style="width:100%; min-height: 500px">
			</div>
		</div>
	</div>
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<p>Tourism is the backbone of our economy. It’s on track to be worth over $11 billion by 2021*</p>
				<p>Get amongst it and be a boss across New Zealand and see what activities are up for grabs!</p>
				<p><small>*Source Ministry of Business Innovation and Employment</small></p>
				<p class="text-muted small" style="margin-top:10px;">Photo &copy; <?= $digitalnz_copyright; ?></p>
			</div>
		</div>
</div>

</div>
<?php
include('_map.php');
include('_footer.php');
