<?php

if(PHP_SAPI !== 'cli') {
	echo 'This script can be run only in command line mode.';
	exit(1);
}

require_once __DIR__ . '/../../common.php';
require_once SYSTEM . 'functions.php';
require_once SYSTEM . 'init.php';

$test = new \Illuminate\Database\Schema\MySqlSchemaState($eloquentConnection);
$test->dump($eloquentConnection, BASE . 'dump.sql');

echo 'Dumped.';
