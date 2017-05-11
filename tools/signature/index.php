<?php
	require('../../common.php');
	require(SYSTEM . 'functions.php');
	require(SYSTEM . 'init.php');

	// Definitions
	define('SIGNATURES', BASE . 'tools/signature/');
	define('SIGNATURES_BACKGROUNDS', 'images/backgrounds/');
	define('SIGNATURES_CACHE', SYSTEM . 'cache/signatures/');
	define('SIGNATURES_DATA', SYSTEM . 'data/');
	define('SIGNATURES_FONTS', SIGNATURES . 'fonts/');
	define('SIGNATURES_IMAGES', SIGNATURES . 'images/');
	define('SIGNATURES_ITEMS', BASE . 'images/items/');

	if(!$config['signature_enabled'])
		die('Signatures disabled.');

	$file = strtolower($config['signature_type']) . '.php';
	if(!file_exists($file))
		die('ERROR: Wrong signature type in config.');

	require($file);
?>
