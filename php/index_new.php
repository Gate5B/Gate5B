<?php
include ('_dbconntect.php');
include ('_header.php');
?>

<div class="container">
	<div class="row">
		<div class="col-sm-3 col-xs-5 col-xs-offset-7">
			<h1><img src="/static/img/tourismBizKitLogo.png" class="img-responsive" alt="Gate 5B Tourism BizKit"></h1>
		</div>
	</div>
		<div class="row">
		<div class="col-sm-4 col-sm-offset-2">
			<h2 class="alt">Your Tourism Business <span style="font-size:150%;display:block;">Gateway</span></h2>
			<p>Thinking about starting a small New Zealand tourism business?</p>
				<p>This tool helps you:
					<ul>
						<li>Explore your idea and tourist activity in your region</li>
						<li>Understand local cultural insights and perspectives</li>
					</ul>
				</p>

				<p>Where are they coming from?  Where are they going? What do they do? What areas are growing?</p>

			<h4 class="text-center" style="margin-top:50px;">Tell us where you want to launch your business <span class="hidden-xs">&rarr;</span><span class="visible-xs-inline">&darr;</span></h4>

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
			</div>
		</div>
</div>

</div>
<?php
include('_map.php');
include('_footer.php');
