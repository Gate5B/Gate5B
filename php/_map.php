    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
    <script>
    	var polygons = {};
    	var infowindow;
    	var map;

		var allowedBounds = new google.maps.LatLngBounds();
		allowedBounds.extend(new google.maps.LatLng(-33.979990178813424, 178.45414478124997)); 
		allowedBounds.extend(new google.maps.LatLng(-48.59648841366372, 166.15620678125003));
		var lastValidCenter;    	
		
    	
		function initialize() {
			var myLatlng = new google.maps.LatLng(-40.805375, 173.351031);
			var mapOptions = {
			zoom: 5,
			draggable: jQuery(window).width() >= 750,
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

			map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);


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
			  content: "<h1 class='map-title'><a href='/activity/<?= $row['tla_id']; ?>/<?= urlencode(strtolower($row['name'])); ?>'><?= $row['name']; ?></a></h1>" +
			  "<a class='map-link' href='/activity/<?= $row['tla_id']; ?>/<?= urlencode(strtolower($row['name'])); ?>'>Explore &gt;</a>",
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

			lastValidCenter = map.getCenter();    	

			google.maps.event.addListener(map, 'center_changed', function() {
				if (allowedBounds.contains(map.getCenter())) {
					// still within valid bounds, so save the last valid position
					lastValidCenter = map.getCenter();
					return; 
				}
			
				// not valid anymore => return to last valid position
				map.panTo(lastValidCenter);
			});			
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