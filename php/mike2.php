<?php

$toUse = json_decode(file_get_contents('../data/_tla_digitalnz.json'), true);
var_dump($toUse);

if ($_GET['use']) {
	$toUse[$_GET['tla']][$_GET['id']] = true;
} else {
	unset($toUse[$_GET['tla']][$_GET['id']]);
}

file_put_contents('../data/_tla_digitalnz.json', json_encode($toUse));