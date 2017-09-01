<?php
	require('../../common.php');
	require(SYSTEM . 'functions.php');
	require(SYSTEM . 'init.php');

	// Definitions
	define('SIGNATURES', TOOLS . 'signature/');
	define('SIGNATURES_BACKGROUNDS', 'images/backgrounds/');
	define('SIGNATURES_CACHE', CACHE . 'signatures/');
	define('SIGNATURES_DATA', SYSTEM . 'data/');
	define('SIGNATURES_FONTS', SIGNATURES . 'fonts/');
	define('SIGNATURES_IMAGES', SIGNATURES . 'images/');
	define('SIGNATURES_ITEMS', BASE . 'images/items/');

	if(!$config['signature_enabled'])
		die('Signatures are disabled on this server.');

	$file = trim(strtolower($config['signature_type'])) . '.php';
	if(!file_exists($file))
		die('ERROR: Wrong signature type in config.');

	putenv('GDFONTPATH=' . SIGNATURES_FONTS);

	if(!isset($_REQUEST['name']))
		die('Please enter name as get or post parameter.');
	
	$name = stripslashes(ucwords(strtolower(trim($_REQUEST['name']))));
	$player = new OTS_Player();
	$player->find($name);
 
	if(!$player->isLoaded())
	{
		header('Content-type: image/png');
		readfile(SIGNATURES_IMAGES.'nocharacter.png');
		exit;
	}
	
	if(!function_exists( 'imagecreatefrompng'))
	{
		header('Content-type: image/png');
		readfile(SIGNATURES_IMAGES.'nogd.png');
		exit;
	}
	
	$cached = SIGNATURES_CACHE.$player->getId() . '.png';
	if (file_exists($cached) and (time() < (filemtime($cached) + (60 * $config['signature_cache_time']))))
	{
		header( 'Content-type: image/png' );
		readfile( SIGNATURES_CACHE.$player->getId().'.png' );
		exit;
	}

	require($file);
?>
