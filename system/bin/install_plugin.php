<?php

if(PHP_SAPI !== 'cli') {
	die('This script can be run only in command line mode.');
}

require_once __DIR__ . '/../../common.php';
require_once SYSTEM . 'functions.php';
require_once SYSTEM . 'init.php';
require_once SYSTEM . 'hooks.php';
require_once LIBS . 'plugins.php';

if($argc !== 2) {
	exit('This command expects one parameter: zip file name (plugin)' . PHP_EOL);
}

$path_to_file = $argv[1];
$ext = strtolower(pathinfo($path_to_file, PATHINFO_EXTENSION));
if($ext !== 'zip') {// check if it is zipped/compressed file
	exit('Please install only .zip files.' . PHP_EOL);
}

if(!file_exists($path_to_file)) {
	exit('ERROR: File ' . $path_to_file . ' does not exist' . PHP_EOL);
}

if(Plugins::install($path_to_file)) {
	foreach(Plugins::getWarnings() as $warning) {
		echo 'WARNING: ' . $warning;
	}

	$info = Plugins::getPlugin();
	echo (isset($info['name']) ? $info['name'] . ' p' : 'P') . 'lugin has been successfully installed.';
}
else
	echo 'ERROR: ' . Plugins::getError();

echo PHP_EOL;