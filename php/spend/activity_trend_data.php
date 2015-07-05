<?php
header ( 'Content-Type: text/json' );
include ('../_dbconntect.php');

$result = array(
	'labels' => array(),
	'data' => array(), 
);


$activity_id = isset($_GET ['activity_id']) ? $_GET ['activity_id'] : '';
if (!$activity_id) {
	// Missing or empty TLA ID
	echo json_encode($result);
	exit;
}

$sqlStmt = "select * from activity_trend where ACTIVITY_ID = ? order by YEAR_END";
$st = $db->prepare ($sqlStmt);
$st->execute(array($activity_id));

foreach ($st->fetchAll() as $trend) {
	$result['labels'][] = $trend['YEAR_END'];
	$result['data'][] = $trend['VISITORS'];
}

echo json_encode ( $result );