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

<div class="jumbotron" style="background-image:url(<?= $digitalnz_image_url; ?>);background-size:cover;background-position:center center;">
	<div class="container">
		<div class="col-xs-9">
			<div class="location-name-holder">
				<h2><?php echo $tla['name'] ?></h2>
			</div>
		</div>
		<div class="col-xs-3">
			<div id="map-canvas" class="mini-map-box"></div>
			<?php include('_mini_map.php'); ?>
		</div>
	</div>
</div>

<div class="container main">
		<div class="row">

			<div>
			<?php
			include ('spend/opportunity.php');
			?>
			</div>
	</div>
	<div class="row">
			<?php
			include ('spend/activity_trend.php');
			?>
	</div>
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<h2>Snapshot</h2>
		</div>
		<div class="col-md-2 col-md-offset-1">
			<?php
			include ('spend/population.php');
			?>
		</div>
		<div class="col-md-8">
			<h4>Tourism businesses in the region</h4>
			<canvas id="myChart" class="opportunity-snapshot-chart" ></canvas>
		</div>
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
		});
	}

	function pageLoad() {
		snapshotChart();
		activityTrend();
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
