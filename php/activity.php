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

$title = sprintf("Popular tourist activities in %s", $tla['name']);
include ('_header.php');
include('_digitalnz.php');

?>
<nav class="navbar navbar-inverse navbar-bizkit">
  <div class="container">
    <div class="navbar-header">
			<div class="col-sm-4 col-xs-6 ">
				<ul class="nav navbar-nav">
	        <li><a href="/">&larr; Back</a></li>
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
<!--<div class="container">
	<div class="row">
		<div class="col-xs-3">
			<a href="/map" type="button" class="btn btn-link">&lt; Home</a>
		</div>
		<div class="col-xs-9">
			<h1><img src="/static/img/tourismBizKitLogoHdr.png" class="img-responsive" style="float:right;" alt="Gate 5B Tourism BizKit"></h1>
		</div>
	</div>
</div>-->
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

		<h3>Popular tourist attractions*</h3>

		<div class="row">
			<div class="col-sm-9 text-center">
				<div id="activity-report"></div>
			</div>


			<div class="col-sm-3">

				<p class="text-muted"><span class="hidden-sm">&larr;</span><span class="visible-sm-inline">&uarr;</span> Select an activity to find out more about it and get an overview of the tourist market in your local area.</p>
			<h4>Projected growth</h4>
			<table class="table">
				<tr>
					<td>
						<img src="/static/img/empty.png" alt="" class="img-circle" style="height:20px;width:20px;background-color:#78A786">
					</td>
					<td>High
					</td>
				</tr>
				<tr>
					<td>
						<img src="/static/img/empty.png" alt="" class="img-circle" style="height:20px;width:20px;background-color:#565f61">
					</td>
					<td>Some
					</td>
				</tr>
				<tr>
					<td>
						<img src="/static/img/empty.png" alt="" class="img-circle" style="height:20px;width:20px;background-color:#401432">
					</td>
					<td>Low to none
					</td>
				</tr>
			</table>
			<p class="hidden-sm hidden-xs text-muted small">Photo &copy; <?= $digitalnz_copyright; ?></p>
			</div>
		</div>


		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<h4>Other popular attractions:</h4>

				<?php
					$st = $db->prepare ( "SELECT * FROM Activity_By_Region WHERE TLA_ID = ? AND (ICON is not null AND ICON not like 'NULL%') order by VISITORS DESC LIMIT 11, 100" );
					$st->execute ( array (
							$tla_id
					) );
					$activities = $st->fetchAll ();

					echo "<table class='table table-striped'>";
					foreach ( $activities as $activity ) {
						echo "<tr><td><a href='/opportunity/" . $tla_id . "/" . $activity['ACTIVITY_ID']  . "/" . urlencode(strtolower($tla['name'])) . "'>" . $activity['ACTIVITY'] . "</a></td></tr>";
					}
					echo "</table>";
				?>
								<button type="button" class="btn btn-link" style="white-space: normal;" id="view-region-btn">Can't find an activity to kick-start your interest? Find out more about the tourism market in your area here &rarr;</button>
			<p>* Analysis based on data from <a href="http://www.med.govt.nz" target="_blank">2013 International Visitor Survey from Ministry of Business, Innovation and Employment survey</a>.</p>
			<p class="visible-xs visible-sm text-muted small" style="margin-top:10px;">Photo &copy; <?= $digitalnz_copyright; ?></p>
			</div>
		</div>


	</div>

	<script>
		function pageLoad() {
			var diameter = 500, format = d3.format(",d"), color = d3.scale.category20c();
			if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
				diameter = 300;
			}

			var bubble = d3.layout.pack()
				.sort(null)
				.size([diameter, diameter])
				.padding(1.5);

			var svg = d3.select("#activity-report").append("svg")
				.attr("width", diameter)
				.attr("height", diameter)
				.attr("class", "bubble");

			var tooltip = d3.select("body")
				.append("div")
				.style("position", "absolute")
				.style("z-index", "10")
				.style("visibility", "hidden")
				.text("a simple tooltip")
				.attr("class", "activity-bubble-text");

			d3.json("/activity_data/<?php echo $tla_id; ?>", function(error, root) {
				if (error) throw error;

				var node = svg.selectAll(".node")
					.data(bubble.nodes(classes(root))
					.filter(function(d) { return !d.children; }))
					.enter().append("g")
					.attr("class", "node")
					.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });

				/*node.append("title").text(function(d) {return d.className; });*/

				node.append("circle")
					.attr("r", function(d) { return d.r; })
					.attr("class", "activity-bubble")
					.style("fill", function(d) { return d.color; })
					.on("mouseover", function(d) {
						tooltip.text(d.className);
						return tooltip.style("visibility", "visible");
					 })
					.on("mousemove", function(d) {return tooltip.style("top", (event.pageY-10)+"px").style("left",(event.pageX+10)+"px");})
					.on("mouseout", function(){return tooltip.style("visibility", "hidden");});

				node.append("svg:image")
					.attr("xlink:href", function(d) { return "/static/img/icon/" + d.icon + ".svg"; })
					.attr("opacity", 1)
					.attr("width", function(d) { return d.r; })
					.attr("height", function(d) { return d.r; })
					.attr("class", "activity-bubble-icon")
					.attr("x", function(d) { return -0.5 * d.r; })
					.attr("y", function(d) { return -0.5 * d.r; })
					.on("mouseover", function(d){
						tooltip.text(d.className);
						return tooltip.style("visibility", "visible");
					})
					.on("mousemove", function(){return tooltip.style("top", (event.pageY-10)+"px").style("left",(event.pageX+10)+"px");})
					.on("mouseout", function(){return tooltip.style("visibility", "hidden");});

				/*node.append("text")
					.attr("dy", ".3em")
					.style("text-anchor", "middle")
					.style("fill", "#FFFFFF")
					.attr("class", "activity-bubble-text")
					.text(function(d) { return d.className.substring(0, d.r / 3); });*/

				node.on('click', function(d){
					window.location.assign("/opportunity/<?php echo $tla_id; ?>/" + d.id + "/<?php echo urlencode(strtolower($tla['name'])); ?>");
				});

				/*node.append("svg:circle")
					.attr("stroke", "black")
					.attr("fill", "aliceblue")
					.attr("r", 50)
					.attr("cx", 52)
					.attr("cy", 52)
					.on("mouseover", function(){return tooltip.style("visibility", "visible");})
					.on("mousemove", function(){return tooltip.style("top", (event.pageY-10)+"px").style("left",(event.pageX+10)+"px");})
					.on("mouseout", function(){return tooltip.style("visibility", "hidden");});*/
			});

			// Returns a flattened hierarchy containing all leaf nodes under the root.
			function classes(root) {
				var classes = [];

				function recurse(name, node) {
					if (node.children) node.children.forEach(function(child) { recurse(node.name, child); });
					else classes.push({packageName: name, className: node.name, value: node.size, color : node.color, icon : node.icon, id : node.id});
				}

				recurse(null, root);
				return {children: classes};
			}

			d3.select(self.frameElement).style("height", diameter + "px");

			$('#view-region-btn').click(function() {
				window.location.assign("/opportunity/<?php echo $tla_id; ?>/<?php echo urlencode(strtolower($tla['name'])); ?>");
			});

		}
	</script>
<?php
include ('_footer.php');
?>
