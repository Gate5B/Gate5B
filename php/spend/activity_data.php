<?php
header ( 'Content-Type: text/json' );
include ('../_dbconntect.php');

$result = array(
	'labels' => array(),
	'data' => array(), 
);

// Get the TLA ID
$tla_id = isset($_GET ['tla_id']) ? $_GET ['tla_id'] : '';
if (!$tla_id) {
	// Missing or empty TLA ID
	echo json_encode($result);
	exit;
}

/*
$sqlStr ='select ifnull(ar_top.ACTIVITY, \'Other\') ACTIVITY, sum(ar.VISITORS) VISITORS from Activity_By_Region ar';
$sqlStr .= ' left join (';
$sqlStr .= '	SELECT * FROM Activity_By_Region WHERE TLA_ID = ? AND VISITORS > 0 order by VISITORS desc limit 10';
$sqlStr .= ' ) ar_top';
$sqlStr .= ' on ar.id = ar_top.id';
$sqlStr .= ' WHERE ar.TLA_ID = ? AND ar.VISITORS > 0';
$sqlStr .= ' group by ifnull(ar_top.ACTIVITY, \'Other\') order by VISITORS';
*/

$sqlStr ='SELECT activity, businesses FROM tla LEFT JOIN nzcom_activities ON (tla.nzcom_region = nzcom_activities.region) WHERE businesses > 0 AND tla.id = ?  order by businesses desc limit 10';

$st = $db->prepare ($sqlStr);
$st->execute ( array (
		$tla_id 
) );


foreach ($st->fetchAll() as $activity ) {
	$result['labels'][] = $activity['activity'];
	$result['data'][] = $activity['businesses'];
}

echo json_encode ( $result );