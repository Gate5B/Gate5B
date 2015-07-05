<?php
include('_dbconntect.php');

?><!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Gate5B: Tourism Toolkit</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
    <style>
      html, body, #map-canvas {
        height: 100%;
        margin: 0;
        padding: 0;
      }

      #map-canvas {
      	  float: left;
      	  width: 100%;
      }


    </style>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
    <script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script>
    	var polygons = {};
    	var infowindow;

		function initialize() {
			var myLatlng = new google.maps.LatLng(-40.805375, 173.361031);
			var mapOptions = {
			zoom: 5,
			center: myLatlng,
      streetViewControl: false,
      mapTypeControl: false,
      panControl: false,
      zoomControl: false,
      styles: [
                  {
                    "featureType": "administrative",
                    "elementType": "labels",
                    "stylers": [
                  {
                    "visibility": "on"
                  }
                  ]
                    },
                  {
                    "featureType": "administrative",
                    "elementType": "labels.text.fill",
                    "stylers": [
                  {
                    "color": "#444444"
                  }
                  ]
                  },
                  {
                    "featureType": "administrative.country",
                    "elementType": "labels",
                    "stylers": [
                  {
                    "visibility": "off"
                  }
                  ]
                  },
                  {
                    "featureType": "administrative.province",
                    "elementType": "labels",
                    "stylers": [
                  {
                    "visibility": "on"
                  },
                  {
                    "hue": "#ff0000"
                  }
                  ]
                  },
                  {
                    "featureType": "landscape",
                    "elementType": "all",
                    "stylers": [
                  {
                    "color": "#f2f2f2"
                  }
                  ]
                  },
                  {
                    "featureType": "landscape",
                    "elementType": "geometry.fill",
                    "stylers": [
                    {
                      "color": "#c4d4c5"
                    }
                    ]
                  },
                  {
                    "featureType": "poi",
                    "elementType": "all",
                    "stylers": [
                    {
                      "visibility": "off"
                    }
                    ]
                  },
                  {
                    "featureType": "road",
                    "elementType": "all",
                    "stylers": [
                    {
                      "saturation": -100
                    },
                    {
                      "lightness": 45
                    },
                    {
                      "visibility": "off"
                    }
                    ]
                  },
                  {
                    "featureType": "road",
                    "elementType": "labels",
                    "stylers": [
                    {
                      "visibility": "off"
                    }
                    ]
                  },
                  {
                    "featureType": "road",
                    "elementType": "labels.text",
                    "stylers": [
                    {
                      "visibility": "off"
                    }
                    ]
                  },
                  {
                    "featureType": "road",
                    "elementType": "labels.icon",
                    "stylers": [
                    {
                      "visibility": "off"
                    }
                    ]
                  },
                  {
                    "featureType": "road.highway",
                    "elementType": "all",
                    "stylers": [
                    {
                      "visibility": "simplified"
                    }
                    ]
                  },
                  {
                    "featureType": "road.arterial",
                    "elementType": "labels.icon",
                    "stylers": [
                    {
                      "visibility": "off"
                    }
                    ]
                  },
                  {
                    "featureType": "transit",
                    "elementType": "all",
                    "stylers": [
                    {
                      "visibility": "off"
                    }
                    ]
                  },
                  {
                    "featureType": "water",
                    "elementType": "all",
                    "stylers": [
                    {
                      "color": "#ffffff"
                    },
                    {
                      "visibility": "on"
                    }
                    ]
                  }
                  ]

			}

			var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);


<?php
	foreach ($db->query('SELECT tla_polygons.*, tla.name FROM tla_polygons LEFT JOIN tla ON (tla.id = tla_polygons.tla_id) WHERE tla_id != 999') as $row):
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
	  polygons[<?= $row['id']; ?>] = new google.maps.Polygon({
		paths: points<?= $row['id']; ?>,
		strokeColor: '#FF0000',
		strokeOpacity: 0.0,
		strokeWeight: 2,
		fillColor: '#FF0000',
		fillOpacity: 0.0
	  });

	  polygons[<?= $row['id']; ?>].setMap(map);
	  polygons[<?= $row['id']; ?>].set('tla', <?= $row['tla_id']; ?>);

	  google.maps.event.addListener(polygons[<?= $row['id']; ?>], 'click', function(e) {
		colorTLA(<?= $row['tla_id']; ?>, 0.8);
		if (infowindow) {
			infowindow.close();
		}
		 infowindow = new google.maps.InfoWindow({
			 position: e.latLng,
			  content: "<h1><a href='/activity/<?= $row['tla_id']; ?>/<?= urlencode(strtolower($row['name'])); ?>'><?= $row['name']; ?></a></h1>" +
			  "<a href='/activity/<?= $row['tla_id']; ?>/<?= urlencode(strtolower($row['name'])); ?>'>More Info &gt;</a>",
			  maxWidth: 200
		  });
		 infowindow.open(map);

	  });

	  google.maps.event.addListener(polygons[<?= $row['id']; ?>], 'mouseover', function() {
		colorTLA(<?= $row['tla_id']; ?>, 0.3);
	  });

	  google.maps.event.addListener(polygons[<?= $row['id']; ?>], 'mouseout', function() {
		colorTLA(<?= $row['tla_id']; ?>, 0.0);
	  });


<?php
	endforeach;
?>
		}

		google.maps.event.addDomListener(window, 'load', initialize);

		function colorTLA(tla_id, opacity) {
			jQuery.each(polygons, function(k, v) {
				if (polygons[k].get('tla') == tla_id) {
					polygons[k].setOptions({fillOpacity: opacity});
				}
			});
		}

    </script>
  </head>
  <body>
    <div id="map-canvas"></div>

    <script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-59726159-3', 'auto');
	  ga('send', 'pageview');

	</script>
  </body>
</html>
