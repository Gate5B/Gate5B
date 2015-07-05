<?php
include_once('_dbconntect.php');

?>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
    <script>
        var polygons = {};
    
        function initialize() {
            var myLatlng = new google.maps.LatLng(-40.805375, 173.361031);
            var mapOptions = {
                zoom: 6,
                disableDefaultUI: true, 
                draggable: false,
                styles: [
                            {
                                "featureType": "water",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#e9e9e9"
                                    },
                                    {
                                        "lightness": 17
                                    }
                                ]
                            },
                            {
                                "featureType": "landscape",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#f5f5f5"
                                    },
                                    {
                                        "lightness": 20
                                    }
                                ]
                            },
                            {
                                "featureType": "road.highway",
                                "elementType": "geometry.fill",
                                "stylers": [
                                    {
                                        "color": "#ffffff"
                                    },
                                    {
                                        "lightness": 17
                                    }
                                ]
                            },
                            {
                                "featureType": "road.highway",
                                "elementType": "geometry.stroke",
                                "stylers": [
                                    {
                                        "color": "#ffffff"
                                    },
                                    {
                                        "lightness": 29
                                    },
                                    {
                                        "weight": 0.2
                                    }
                                ]
                            },
                            {
                                "featureType": "road.arterial",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#ffffff"
                                    },
                                    {
                                        "lightness": 18
                                    }
                                ]
                            },
                            {
                                "featureType": "road.local",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#ffffff"
                                    },
                                    {
                                        "lightness": 16
                                    }
                                ]
                            },
                            {
                                "featureType": "poi",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#f5f5f5"
                                    },
                                    {
                                        "lightness": 21
                                    }
                                ]
                            },
                            {
                                "featureType": "poi.park",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#dedede"
                                    },
                                    {
                                        "lightness": 21
                                    }
                                ]
                            },
                            {
                                "elementType": "labels.text.stroke",
                                "stylers": [
                                    {
                                        "visibility": "on"
                                    },
                                    {
                                        "color": "#ffffff"
                                    },
                                    {
                                        "lightness": 16
                                    }
                                ]
                            },
                            {
                                "elementType": "labels.text.fill",
                                "stylers": [
                                    {
                                        "saturation": 36
                                    },
                                    {
                                        "color": "#333333"
                                    },
                                    {
                                        "lightness": 40
                                    }
                                ]
                            },
                            {
                                "elementType": "labels.icon",
                                "stylers": [
                                    {
                                        "visibility": "off"
                                    }
                                ]
                            },
                            {
                                "featureType": "transit",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#f2f2f2"
                                    },
                                    {
                                        "lightness": 19
                                    }
                                ]
                            },
                            {
                                "featureType": "administrative",
                                "elementType": "geometry.fill",
                                "stylers": [
                                    {
                                        "color": "#fefefe"
                                    },
                                    {
                                        "lightness": 20
                                    }
                                ]
                            },
                            {
                                "featureType": "administrative",
                                "elementType": "geometry.stroke",
                                "stylers": [
                                    {
                                        "color": "#fefefe"
                                    },
                                    {
                                        "lightness": 17
                                    },
                                    {
                                        "weight": 1.2
                                    }
                                ]
                            }
                        ],
                center: myLatlng
                
            }
            
            var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
            var bounds = new google.maps.LatLngBounds();
<?php
    foreach ($db->query('SELECT * FROM tla_polygons WHERE tla_id = ' . $tla_id) as $row):
?>
            
            // Extend the extent of the region
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
                echo "			bounds.extend(new google.maps.LatLng($lat, $lng));\n";
            }

        }
?>
            // Add all the points for the region
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
                $toOutput[] = "	  			new google.maps.LatLng($lat, $lng)\n";
            }

        }
        if (isset($toOutput[1])) {
            $toOutput[] = $toOutput[1];
        }
        echo implode($toOutput, ',');
?>
            ];

            // Draw the region on the map
            polygons[<?= $row['id']; ?>] = new google.maps.Polygon({
                paths: points<?= $row['id']; ?>,
                strokeColor: '#009900',
                strokeOpacity: 0.4,
                strokeWeight: 2,
                fillColor: '#009900',
                fillOpacity: 0.2
            });
            polygons[<?= $row['id']; ?>].setMap(map);
            
            // Clicking the polygon takes you back to the start
            google.maps.event.addListener(polygons[<?= $row['id']; ?>], 'click', function(e) {
                window.location = "/map";
            });
            
<?php
    endforeach;
?>

            // Zoom to show the whole region
            map.fitBounds(bounds);
            
            // Clicking the map takes you back to the start
            google.maps.event.addListener(map, 'click', function(e) {
                window.location = "/map";
            });
        }
  
        // Draw the map one the page loads
        google.maps.event.addDomListener(window, 'load', initialize);
    </script>