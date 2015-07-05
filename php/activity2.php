<?php
include ('_dbconntect.php');

$tla_id = isset($_GET ['tla']) ? $_GET ['tla'] : '';
$st = $db->prepare('SELECT * FROM tla WHERE tla.id = ?');
$st->execute(array($tla_id));
$tla = current($st->fetchAll());
if (!$tla_id || !$tla) {
	// Invalid TLA ID
	header('Location: /map');
	exit;
}

$title = sprintf("Popular tourist activities in %s", $tla['name']);
include ('_header.php');

?>
<div class="jumbotron">
	<div class="container">
		<h2><small>Popular tourist activities in</small><br><?php echo $tla['name'] ?></h2>
	</div>
</div>
	<div class="container main">
		<?php include('_digitalnz.php'); ?>
<?php
include ('_footer.php');

