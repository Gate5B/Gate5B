<?php
include ('_dbconntect.php');

$tla_id = isset($_GET ['tla']) ? $_GET ['tla'] : '';
$st = $db->prepare('SELECT * FROM tla WHERE tla.id = ?');
$st->execute(array($tla_id));
$tla = current($st->fetchAll());
if (!$tla_id || !$tla) {
	// Invalid TLA ID
	header('Location: /map');
	exit;
}

$title = sprintf("Opportunities in %s", $tla['name']);
include ('_header.php');
include('_digitalnz.php');

// var_dump($_GET['activity']);
?>
<nav class="navbar navbar-inverse navbar-bizkit">
  <div class="container">
    <div class="navbar-header">
			<div class="col-sm-4 col-xs-6 ">
				<ul class="nav navbar-nav">
	        <li><a href="/activity/<?php echo $tla_id; ?>/<?php echo $tla['name'] ?>">&larr; Back</a></li>
				</ul>
		</div>
			<div class="col-sm-4 col-sm-offset-3 col-xs-6 ">
      <a class="navbar-brand" href="#">
        <img alt="TouristBizKit" src="/static/img/tourismBizKitLogoHdr.png" class="img-responsive">
      </a>
		</div>
    </div>
  </div>
</nav>
<div class="jumbotron" style="background-image:url(<?= $digitalnz_image_url; ?>);background-size:cover;background-position:center center;">
	<div class="container">
		<div class="col-xs-9">
			<div class="location-name-holder">
				<h2><?php echo $tla['name'] ?></h2>
			</div>
		</div>
		<div class="col-xs-3">

		</div>
	</div>
</div>

<div class="container main">
	<div class="row">
			<?php
			include ('spend/activity_trend.php');
			?>
	</div>
	<div class="row">
		<div class="col-md-12"><hr /></div>
	</div>
	<div class="row">
		<div class="col-md-2 col-md-offset-1 text-center">
			<h4 class="text-center">Population</h4>
			<?php
			include ('spend/population.php');
			?>
		</div>
		<div class="col-md-8" style="border-left: 1px solid #eeeeee;">
						<h2>Current tourism operators</h2>
			<p class="text-muted">Region population and the current types and number of tourism operators in your area*</p>
			<canvas id="myChart" class="opportunity-snapshot-chart" ></canvas>
			<p class="text-muted small">* Data based on <a href="http://newzealand.com" target="_blank">newzealand.com</a> data on New Zealand tourism operations </p>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12"><hr /></div>
	</div>
		<div class="row">

			<?php
			include ('spend/opportunity.php');
			?>
	</div>


	<div class="row">
		<!--h2>Call to market</h2-->
			<?php
			include ('spend/calltomarket.php');
			?>

	</div>
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
		<p class="text-muted small" style="margin-top:10px;">Photo &copy; <?= $digitalnz_copyright; ?></p>
	</div>
	</div>
</div>
<script>


	function snapshotChart() {
		var data = {
			labels: [],
			datasets: [
				{
					label: "My First dataset",
					fillColor: "rgba(220,220,220,0.5)",
					strokeColor: "rgba(220,220,220,0.8)",
					highlightFill: "rgba(220,220,220,0.75)",
					highlightStroke: "rgba(220,220,220,1)",
					data: []
				},

			]
		};

		var jsonData = $.getJSON("/php/spend/activity_data.php?tla_id=<?php echo $tla_id; ?>", function(jsonData) {
			$.each(jsonData.labels, function(position, dataItem) {
				data.labels.push(dataItem);
			});
			$.each(jsonData.data, function(position, dataItem) {
				data.datasets[0].data.push(dataItem);
			});

			var ctx = $("#myChart").get(0).getContext("2d");

			var myBarChart = new Chart(ctx).Bar(data);
			// myBarChart.redraw();
		});
	}

	function pageLoad() {
		snapshotChart();
		if (typeof activityTrend === 'function')
		{
			activityTrend();
		}

	}


</script>
<?php
$st = $db->prepare ( 'SELECT * FROM tla LEFT JOIN nzcom_activities ON (tla.nzcom_region = nzcom_activities.region) WHERE businesses > 0 AND tla.id = ?' );
$st->execute ( array (
		$tla_id
) );
$activities = $st->fetchAll ();
/*
echo "<table>";
foreach ( $activities as $activity ) {
	echo "<tr><td>" . $activity ['activity'] . "</td><td>" . $activity ['businesses'] . "</td></tr>";
}
echo "</table>";
*/
?>
<?php

include ('_footer.php');
?>
