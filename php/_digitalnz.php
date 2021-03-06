<?php
include_once('_dbconntect.php');

$images = array();
foreach ($db->query('SELECT * FROM tla_digitalnz WHERE tla_id = ' . $tla_id) as $row) {
	$images[] = $row;
}
shuffle($images);

$digitalnz_caption = $digitalnz_image_url = $digitalnz_copyright = '';
if ($images) {
	$row = current($images);
	$image = @json_decode(file_get_contents("http://api.digitalnz.org/v3/records/$row[digitalnz_id].json?api_key=a36ee83d25727cb07d0d2344e10c008a"), true);
	if ($image) {
		$image = $image['record'];
		$digitalnz_caption = $image['title'] . "\n";
		$digitalnz_caption .= $image['description'] . "\n";
		$digitalnz_caption .= $image['display_content_partner'] . "\n";
		$digitalnz_caption .= current($image['copyright']) . "\n";
		$digitalnz_caption = htmlentities($digitalnz_caption);
		
		$digitalnz_image_url = $image['large_thumbnail_url'];

		$digitalnz_copyright = current($image['copyright']) . " " . $image['display_content_partner'];		
	}
}