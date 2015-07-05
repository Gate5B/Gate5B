<!DOCTYPE html>
<html lang="en">

<head>
  <title>Gate5B<?= (isset($title)) ? ": $title" : ''; ?></title> 
    <script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>

    <style>
    	a {
    		display: block;
    		float: left;
    		margin: 8px;
    	}
    	
    	a.use {
    		margin: 3px;
    	}

    	a.use img {
    		border: 5px solid #00ff00;
    	}
    	
    	h1 {
    		clear: both;
    	}
	</style>
</head>

<body class="gate5b">

<?php
include('_dbconntect.php');

$toUse = json_decode(file_get_contents('../data/_tla_digitalnz.json'), true);
$start = isset($_GET['start']) ? $_GET['start'] : 0; 

$count = 0;
$lastname = '';
$lat_max = $lng_max = -360;
$lat_min = $lng_min = 360;
foreach ($db->query('SELECT tla.* FROM tla ORDER BY name LIMIT ' . $start . ', 5') as $row) {
	$lastname = $row['name'];
	echo "<h1>$lastname</h1>";
	
	$row['name'] = str_replace(array('District', 'City'), '', $row['name']);
			
	$lastname = urlencode($row['name']);
	$url = "http://api.digitalnz.org/v3/records.json?api_key=a36ee83d25727cb07d0d2344e10c008a&sort=date&directorion=asc&text=$lastname&and[category][]=Images&per_page=150";
	$data = json_decode(file_get_contents($url), true);
	$displayed = array();
	foreach($data['search']['results'] as $res) {
		if (!$res['thumbnail_url']) {
			continue;
		}
		
		if (isset($displayed[$res['id']])) {
			continue;
		}
		$displayed[$res['id']] = true;
		
		$title = htmlentities($res['title'] . "\n" . $res['description']);
		$use = isset($toUse[$row['id']][$res['id']]) ? 'class="use" ' : '';
		echo "<a data-did='$res[id]' data-tla='$row[id]' title='$title' $use><img width=90 height=90 src='$res[thumbnail_url]'></a>";
		
	}		

	$lastname = urlencode($row['name'] . ' landscape');
	$url = "http://api.digitalnz.org/v3/records.json?api_key=a36ee83d25727cb07d0d2344e10c008a&sort=date&directorion=asc&text=$lastname&and[category][]=Images&per_page=150";
	$data = json_decode(file_get_contents($url), true);
	foreach($data['search']['results'] as $res) {
		if (!$res['thumbnail_url']) {
			continue;
		}
		
		if (isset($displayed[$res['id']])) {
			continue;
		}
		$displayed[$res['id']] = true;		
		
		$title = htmlentities($res['title'] . "\n" . $res['description']);
		$use = isset($toUse[$row['id']][$res['id']]) ? 'class="use" ' : '';
		echo "<a data-did='$res[id]' data-tla='$row[id]' title='$title' $use><img width=90 height=90 src='$res[thumbnail_url]'></a>";
		
	}		

}

?>

<?php if ($start): ?>
<h1><a href="mike.php?start=<?= $start - 5 ?>">Prev</a></h1>
<?php endif; ?>

<h1><a href="mike.php?start=<?= $start + 5 ?>">Next</a></h1> 

    <script type="text/javascript">
    	jQuery('a').on('click', function(e) {
			$link = jQuery(this);
			if (!$link.data('tla')) {
				return;
			}
			$link.toggleClass('use');
			if (!$link.hasClass('use')) {
				jQuery.getJSON("/php/mike2.php?use=0&tla=" + $link.data('tla') + '&id=' + $link.data('did')); 
			} else if ($link.hasClass('use')) {
				jQuery.getJSON("/php/mike2.php?use=1&tla=" + $link.data('tla') + '&id=' + $link.data('did')); 
			}
			e.preventDefault();
    	});
    </script>
</body>
</html>