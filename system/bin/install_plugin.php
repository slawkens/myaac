<?php

if(PHP_SAPI !== 'cli') {
	echo 'This script can be run only in command line mode.';
	exit(1);
}

require_once __DIR__ . '/../../common.php';
require_once SYSTEM . 'functions.php';
require_once SYSTEM . 'init.php';
require_once SYSTEM . 'hooks.php';
require_once LIBS . 'plugins.php';

if($argc !== 2) {
	echo 'This command expects one parameter: zip file name (plugin)' . PHP_EOL;
	exit(2);
}

$path_to_file = $argv[1];
$ext = strtolower(pathinfo($path_to_file, PATHINFO_EXTENSION));
if($ext !== 'zip') {// check if it is zipped/compressed file
	echo 'Please install only .zip files.' . PHP_EOL;
	exit(3);
}

if(!file_exists($path_to_file)) {
	echo 'ERROR: File ' . $path_to_file . ' does not exist' . PHP_EOL;
	exit(4);
}

if(Plugins::install($path_to_file)) {
	foreach(Plugins::getWarnings() as $warning) {
		echo 'WARNING: ' . $warning;
	}

	$info = Plugins::getPluginJson();
	echo (isset($info['name']) ? $info['name'] . ' p' : 'P') . 'lugin has been successfully installed.' . PHP_EOL;
}
else {
	echo 'ERROR: ' . Plugins::getError() . PHP_EOL;
	exit(5);
}
