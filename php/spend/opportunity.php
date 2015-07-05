<?php

$result = array(
	'visitor_num' => 0,
	'visitor_growth' => 0,
	'from_origin' => array(),
	'stay_nights' => array(),
	'stay' => array(),
	'spend_num' => 0,
	'spend_forecast' => 0
);
//
//$sqlStr ='SELECT VISITORS FROM IVS_TLA_Forecast where TLA_ID = ? and year(YEAR_END) = 2018 ';
$sqlStr =" SELECT sum(VISITORS) VISITORS FROM IVS_TLA   where tla_id = ? and YEAR_END = '2013-03-01' ";

$st = $db->prepare ($sqlStr);
$st->execute ( array (
		$tla_id
) );
foreach ($st->fetchAll() as $forecast ) {

	$result['visitor_growth'] = $forecast['VISITORS'] ;

}


// Visitors From by Region
$sqlStr ='SELECT T.COUNTRY_NAME, SUM(T.VISITORS) VISITORS, SUM(F.VISITORS) VISITORS_2018 FROM IVS_TLA T LEFT JOIN IVS_Origin_Forecast F ON T.TLA_ID = F.TLA_ID and T.COUNTRY_ID = F.COUNTRY_ID where T.tla_id = ? and year(T.YEAR_END) = 2015 GROUP by T.COUNTRY_NAME ORDER BY T.VISITORS DESC';
//$sqlStr ="SELECT T.COUNTRY_NAME, SUM(T.VISITORS) VISITORS, SUM(F.VISITORS) VISITORS_2018 FROM IVS_TLA T LEFT JOIN IVS_TLA F ON T.TLA_ID = F.TLA_ID and T.COUNTRY_ID = F.COUNTRY_ID where T.tla_id = ? and year(T.YEAR_END) = '2013-03-01' GROUP by T.COUNTRY_NAME ORDER BY T.VISITORS DESC";

$st = $db->prepare ($sqlStr);
$st->execute ( array (
		$tla_id
) );
foreach ($st->fetchAll() as $activity ) {

	$result['visitor_num'] = $result['visitor_num'] + (int) $activity['VISITORS'];
	$origin = array('origin'=> $activity['COUNTRY_NAME'], 'number'=> (int) $activity['VISITORS'], 'forecast'=> (int) $activity['VISITORS_2018']);
	$result['from_origin'][] = $origin;
}

// Spend by Region
$sqlStr ="SELECT sum(AMOUNT_MILLIONS) AMOUNT_MILLIONS FROM RTE_TA where tla_id = ? and year_end = 2014";

$st = $db->prepare ($sqlStr);
$st->execute ( array (
		$tla_id
) );
foreach ($st->fetchAll() as $activity ) {

	$result['spend_num'] = $result['spend_num'] + (int) $activity['AMOUNT_MILLIONS'];
	//echo($activity['AMOUNT_MILLIONS']);
}

$sqlStr ="SELECT sum(AMOUNT_MILLIONS) AMOUNT_MILLIONS FROM RTE_Forecast where tla_id = ? and year_end = 2018";

$st = $db->prepare ($sqlStr);
$st->execute ( array (
		$tla_id
) );
foreach ($st->fetchAll() as $activity ) {

	$result['spend_forecast'] = $result['spend_forecast'] + (int) $activity['AMOUNT_MILLIONS'];
	//echo($activity['AMOUNT_MILLIONS']);
}

// Night Stay by Country
$sqlStr = "SELECT year(YEAR_END), ACCOMMODATION_CODE, SUM(DAYS *VISITORS) DAYS, SUM(VISITORS) VISITORS FROM IVS_Accom where year(YEAR_END) = 2015 group by year(YEAR_END), ACCOMMODATION_CODE";

$st = $db->prepare ($sqlStr);
$st->execute ( array (
		$tla_id
) );
$days = 0;
$visitor_num = 0;
foreach ($st->fetchAll() as $activity ) {
	$days = $days + $activity['DAYS'];
	$visitor_num = $visitor_num + $activity['VISITORS'];
	//$result['spend_num'] + (int) $activity['DAYS'];
	$stay = array('type'=> $activity['ACCOMMODATION_CODE'], 'avg_days'=> (int) $activity['DAYS'] / $activity['VISITORS']);
	$result['stay'][] = $stay;
}

$result['stay_nights'] =  $days /$visitor_num;

foreach ($st->fetchAll() as $activity ) {

	$result['spend_num'] = $result['spend_num'] + (int) $activity['AMOUNT_MILLIONS'];
}


?>

<div class="col-md-10 col-md-offset-1">
	<h2>Tourism in your region</h2>
</div>
<div class="col-md-10 col-md-offset-1">
<div class="opportunity-panel">
	<h3 class="opportunity-panel-title">Visitors</h3>
	<ul class="info-box">
		<li>
			<div class="opportunity-label">2015</div>
			<div class="opportunity-detail"><?php echo  number_format (round($result['visitor_num']/1000, 0)) ?> K</div>
		</li>
		<li>
			<div class="opportunity-label">2018</div>
			<div class="opportunity-detail"><?php
				//$percentage = round((($result['visitor_growth']-$result['visitor_num'])/$result['visitor_num'])*100, 2);
				$percentage = round((($result['visitor_num']-$result['visitor_growth'])/$result['visitor_growth'])*100, 2);
				$class_type = 'red';
				$sign = '';
				if($percentage>0){
					$sign = '+';
					$class_type = 'green';
				}
				echo "<span class='indicator-".$class_type."'>";
				echo  $sign .number_format ($percentage) ;
				echo "%</span>";
			?></div>
		</li>
	</ul>
	<div class="col-xs-12">
		<p class="small text-muted">Number of annual visitors in the region and forecasted % change in 2018</p>
	</div>
</div>

<div class="opportunity-panel">
	<h3 class="opportunity-panel-title">Average Stay</h3>
		<ul class="info-box">
			<li>
				<div class="opportunity-label">2015</div>
				<div class="opportunity-detail"><?php echo  number_format (round($result['stay_nights'], 1)) ?> nights</div>
			</li>
			<li>
				<div class="opportunity-label">2018</div>
				<div class="opportunity-detail"><span class="indicator-green">+1 %</span></div>
			</li>
		</ul>
		<div style="min-width:100%; display:none;">
			<ul class="info-box opportunity-stay">

				<?php
					for ( $i = 0; $i < 5 && $i < count($result['stay']); $i++ ) {
						$stay = $result['stay'][$i] ;
				?>
				<li>
					<div class="opportunity-label"><?php echo  $stay['type'] ?></div>
					<div class="opportunity-detail"><?php echo  number_format (round($stay['avg_days'], 1)) ?></div>
				</li>
				<?php
					}
				?>
			</ul>
		</div>
		<div class="col-xs-12">
			<p class="small text-muted">Average number of nights stayed in NZ by visitors and forecasted % change in 2018</p>
		</div>
</div>

<div class="opportunity-panel">

	<h3 class="opportunity-panel-title">Come from</h3>
	<table class="opportunity-from-table" style="width:100%">
		<tr>
			<th>Top 5</th>
			<th>2015</th>
			<th>2018</th>
		</tr>
		<?php
			for ( $i = 0; $i < 5 && $i < count($result['from_origin']); $i++ ) {
				$origin = $result['from_origin'][$i]
		?>
		<tr>
			<td><?php echo  $origin['origin'] ?></td>
			<td><?php echo  number_format (round($origin['number']/100, 0)) ?> K</td>
			<td><?php
				$percentage = round((($origin['forecast']-$origin['number'])/$origin['number'])*100, 0);

				$class_type = 'red';
				$sign = '';
				if($percentage>0){
					$sign = '+';
					$class_type = 'green';
				}
				echo "<span class='indicator-".$class_type."'>";
				echo  $sign .number_format ($percentage) ;
				echo "%</span>";
			?></td>
		</tr>
		<?php
			}
		?>
	</table>
	<div class="col-xs-12">
		<p class="small text-muted">Top five places where tourists have come and future view of growth in 2018</p>
	</div>
</div>

<div class="opportunity-panel">

	<h3 class="opportunity-panel-title">Spend</h3>
	<ul class="info-box">
		<li>
			<div class="opportunity-label">2014</div>
			<div class="opportunity-detail">$<?php echo  number_format (round($result['spend_num'], 0)) ?> M</div>
		</li>
		<li>
			<div class="opportunity-label">2018</div>
			<div class="opportunity-detail"><?php
				$percentage = round((($result['spend_forecast']-$result['spend_num'])/$result['spend_num'])*100, 2);
				$class_type = 'red';
				$sign = '';
				if($percentage>0){
					$sign = '+';
					$class_type = 'green';
				}
				echo "<span class='indicator-".$class_type."'>";
				echo  $sign .number_format ($percentage) ;
				echo "%</span>";
			?></div>
		</li>
	</ul>
	<div class="col-xs-12">
		<p class="small text-muted">Total annual spend in the region and forecasted % change in 2018</p>
	</div>
</div>
</div>
