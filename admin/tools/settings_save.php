<?php

use MyAAC\Services\LoginService;
use MyAAC\Settings;

const MYAAC_ADMIN = true;

require '../../common.php';
require SYSTEM . 'functions.php';
require SYSTEM . 'init.php';

$loginService = new LoginService();
$loginService->checkLogin();

if(!admin()) {
	http_response_code(500);
	die('Access denied.');
}

csrfProtect();

if (!isset($_REQUEST['plugin'])) {
	http_response_code(500);
	die('Please enter plugin name.');
}

if (!isset($_POST['settings'])) {
	http_response_code(500);
	die('Please enter settings.');
}

$settings = app()->get('settings');

$success = $settings->save($_REQUEST['plugin'], $_POST['settings']);

$errors = $settings->getErrors();
if (count($errors) > 0) {
	http_response_code(500);
	die(implode('<br/>', $errors));
}

if ($success) {
	echo 'Saved at ' . date('H:i');
}
