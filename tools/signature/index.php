<?php
	require('../../common.php');
	require(SYSTEM . 'functions.php');
	require(SYSTEM . 'init.php');

	if(!$config['signature_enabled'])
		die('Signatures disabled.');

	$file = strtolower($config['signature_type']) . '.php';
	if(!file_exists($file))
		die('ERROR: Wrong signature type in config.');

	$cacheMinutes = 5;
	require($file);
?>
