<?php

if(PHP_SAPI !== 'cli') {
	echo 'This script can be run only in command line mode.';
	exit(1);
}

require_once __DIR__ . '/../../common.php';
require_once SYSTEM . 'functions.php';
require_once SYSTEM . 'init.php';

if(clearCache()) {
	echo 'Cache cleared.' . PHP_EOL;
}
else {
	echo 'Unexpected error.' . PHP_EOL;
	exit(2);
}
