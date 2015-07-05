<?php
$result = 0;
// Visitors and From by Region
$sqlStr ='SELECT sum(POPULATION) POPULATION FROM TA_Population where TLA_ID = ? and YEAR_END = 2014';

$st = $db->prepare ($sqlStr);
$st->execute ( array (
		$tla_id
) );
foreach ($st->fetchAll() as $population ) {

	$result = $result + (int) $population['POPULATION'];
}
?>
<div class="opportunity-population">
	<div class="opportunity-population-label">Population</div>
	<div class="opportunity-population-detail"><?php echo  number_format (round($result/1000, 0)) ?>K</div>
</div>
