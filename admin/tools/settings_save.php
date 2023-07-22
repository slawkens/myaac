<?php
const MYAAC_ADMIN = true;

require '../../common.php';
require SYSTEM . 'functions.php';
require SYSTEM . 'init.php';
require SYSTEM . 'login.php';

if(!admin()) {
	http_response_code(500);
	die('Access denied.');
}

if (!isset($_REQUEST['plugin'])) {
	http_response_code(500);
	die('Please enter plugin name.');
}

if (!isset($_POST['settings'])) {
	http_response_code(500);
	die('Please enter settings.');
}

$settings = Settings::getInstance();

$settings->save($_REQUEST['plugin'], $_POST['settings']);

echo 'Saved at ' . date('H:i');
