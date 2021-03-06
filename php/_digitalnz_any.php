<?php
include_once('_dbconntect.php');
/*
$images = array();
foreach ($db->query('SELECT * FROM tla_digitalnz') as $row) {
	$images[] = $row;
}
shuffle($images);
*/
$images = array(
//	33531955,
	31911455,
	33515306,
	35297372,
	35297372,
	35297372,
	35332132,
	30554823
);
shuffle($images);

$digitalnz_caption = $digitalnz_image_url = $digitalnz_copyright = '';
if ($images) {
	$row = array('digitalnz_id' => current($images));
	//$row = current($images);
	//var_dump($row);
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