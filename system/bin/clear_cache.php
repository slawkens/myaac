<?php

if(PHP_SAPI !== 'cli') {
	die('This script can be run only in command line mode.');
}

require_once __DIR__ . '/../../common.php';
require_once SYSTEM . 'functions.php';
require_once SYSTEM . 'init.php';

if(clearCache()) {
	echo 'Cache cleared.';
}
else {
	echo 'Unexpected error.';
}

echo PHP_EOL;