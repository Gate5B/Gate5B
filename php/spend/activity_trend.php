<?php
	if (isset($_GET['activity'])) {
		$activity_id =  $_GET['activity'];
		$st = $db->prepare("SELECT * FROM Activity_By_Region WHERE ACTIVITY_ID = ? AND (ICON is not null AND ICON not like '%blank%') LIMIT 1");
		$st->execute(array($activity_id));

		$name = "";
		$icon = "";
		foreach ($st->fetchAll() as $activity) {
			$name = $activity['ACTIVITY'];
			$icon = $activity['ICON'];
			break;
		}

		$sqlStmt = "select * from activity_trend where ACTIVITY_ID = ? order by YEAR_END";
		$st = $db->prepare ($sqlStmt);
		$st->execute(array($activity_id));

		$showTrend = true;
		foreach ($st->fetchAll() as $trend) {
			$result['labels'][] = $trend['YEAR_END'];
			$result['data'][] = $trend['VISITORS'];
			if (strcmp ($trend['VISITORS'], "0") == 0) {
				$showTrend = false;
			}
		}

		if (strlen($name) != 0 && strlen($icon) != 0 && $showTrend) {


?>

<div class="col-md-offset-1 col-md-10">
	<h2>Is the activity becoming more popular?</h2>
</div>
<div id="activity-trend" class="col-md-2 col-md-offset-1">
	<div id="activity-icon">
		<!--<?php echo $name; ?>-->
	</div>
</div>
<div class="col-md-8">
	<p class="text-muted">The growth of the activity over the past 3 year based on visitor numbers</p>
	<canvas id="activity-trend-panel" class="activity-trends" ></canvas>
</div>


<script>

	function activityTrend() {
		try
		{
		
		
		var trendData = {
			labels: [],
			datasets: [{
					label: "Trend Data",
					fillColor: "rgba(220,220,220,0.5)",
					strokeColor: "rgba(220,220,220,0.8)",
					highlightFill: "rgba(220,220,220,0.75)",
					highlightStroke: "rgba(220,220,220,1)",
					data: []
				}]
		};

		$.getJSON("/php/spend/activity_trend_data.php?activity_id=<?php echo $_GET['activity']; ?>", function(jsonData) {
			$.each(jsonData.labels, function(position, dataItem) {
				trendData.labels.push(dataItem);
			});
			$.each(jsonData.data, function(position, dataItem) {
				trendData.datasets[0].data.push(dataItem);
			});

			var ctx = $("#activity-trend-panel").get(0).getContext("2d");

			var myBarChart = new Chart(ctx).Line(trendData);
		});


		var tooltip = d3.select("body")
				.append("div")
				.style("position", "absolute")
				.style("z-index", "10")
				.style("visibility", "hidden")
				.text("a simple tooltip")
				.attr("class", "activity-bubble-text");

		var activityIconContainer = d3.select("#activity-icon").append("svg").attr("width", 200).attr("height", 200);
		var circle = activityIconContainer.append("circle").attr("cx", 50).attr("cy", 50).attr("r", 50).style("fill", "#153f41")
					.on("mouseover", function() {tooltip.text("<?php echo $name; ?>");
						return tooltip.style("visibility", "visible"); })
					.on("mousemove", function(){return tooltip.style("top", (event.pageY-10)+"px").style("left",(event.pageX+10)+"px");})
					.on("mouseout", function(){return tooltip.style("visibility", "hidden");});

		activityIconContainer.append("svg:image").attr("xlink:href", "/static/img/icon/<?php echo $icon; ?>.svg")
					.attr("width", 50)
					.attr("height", 50)
					.attr("x", 25)
					.attr("y", 30).on("mouseover", function() {tooltip.text("<?php echo $name; ?>");
						return tooltip.style("visibility", "visible"); })
					.on("mousemove", function(){return tooltip.style("top", (event.pageY-10)+"px").style("left",(event.pageX+10)+"px");})
					.on("mouseout", function(){return tooltip.style("visibility", "hidden");});
		}
		catch (e)
		{
			//ignore
		}

	}
</script>

<?php
		}
	}
?>
