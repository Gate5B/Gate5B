<?php
include('_dbconntect.php');

?>


<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Map Test</title>
    <style>
      html, body, #map-canvas {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      
      #map-canvas {
      	  float: left;
      	  width: 70%;
      }

      #events {
      	  float: left;
      	  width: 30%;
      }
      
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true"></script>
    <script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script>
function initialize() {
  var myLatlng = new google.maps.LatLng(-40.805375, 173.361031);
  var mapOptions = {
    zoom: 6,
    center: myLatlng
  }

  var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
  
<?php
	foreach ($db->query('SELECT * FROM region_polygons WHERE tla_id != 999') as $row):
?>

	  var points<?= $row['id']; ?> = [
<?php
		$pairs = explode(',', $row['coordinates']);
		$toOutput = array();
		foreach($pairs as $k => $pair) {
			if (!trim($pair)) {
				unset($pairs[$k]);
				continue;
			}
			@list($lng, $lat) = explode(" ", $pair);
			if ($lng && $lat) {
				$toOutput[] = "new google.maps.LatLng($lat, $lng)\n";
			}

		}
		if (isset($toOutput[1])) {
			$toOutput[] = $toOutput[1];
		}
		echo implode($toOutput, ',');
?>
	  ];

	  // Construct the polygon.
	  polygon<?= $row['id']; ?> = new google.maps.Polygon({
		paths: points<?= $row['id']; ?>,
		strokeColor: '#FF0000',
		strokeOpacity: 0.0,
		strokeWeight: 2,
		fillColor: '#FF0000',
		fillOpacity: 0.0
	  });
	
	  polygon<?= $row['id']; ?>.setMap(map);
	  
	  google.maps.event.addListener(polygon<?= $row['id']; ?>, 'click', function() {
		jQuery('#events').prepend("TLA<?= $row['tla_id']; ?> clicked: <?= $row['name']; ?> <br/>"); 
		polygon<?= $row['id']; ?>.setOptions({fillOpacity: 0.8});
	  });  
	  
	  google.maps.event.addListener(polygon<?= $row['id']; ?>, 'mouseover', function() {
		jQuery('#events').prepend("TLA<?= $row['tla_id']; ?> mouseover: <?= $row['name']; ?> <br/>");
		polygon<?= $row['id']; ?>.setOptions({fillOpacity: 0.3});
	  });  	  
	  
	  google.maps.event.addListener(polygon<?= $row['id']; ?>, 'mouseout', function() {
		jQuery('#events').prepend("TLA<?= $row['tla_id']; ?> mouseout: <?= $row['name']; ?> <br/>");
		polygon<?= $row['id']; ?>.setOptions({fillOpacity: 0});
	  });  	  
	  
	  
<?php
	endforeach;
?>
}
  
google.maps.event.addDomListener(window, 'load', initialize);

    </script>
  </head>
  <body>
    <div id="map-canvas"></div>
    <div id="events"></div>
  </body>
</html>
