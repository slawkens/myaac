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
	echo 'This command expects one parameter: plugin name' . PHP_EOL;
	exit(2);
}

$pluginName = $argv[1];
if(Plugins::executeInstall($pluginName)) {
	foreach(Plugins::getWarnings() as $warning) {
		echo 'WARNING: ' . $warning;
	}

	$info = Plugins::getPluginJson();
	echo 'Script for install ' . (isset($info['name']) ? $info['name'] . ' p' : 'P') . 'lugin has been successfully executed.' . PHP_EOL;
}
else {
	echo 'ERROR: ' . Plugins::getError() . PHP_EOL;
	exit(3);
}
