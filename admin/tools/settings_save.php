<?php

use MyAAC\Settings;

const MYAAC_ADMIN = true;
const IGNORE_SET_LAST_VISIT = true;

require '../../common.php';
require SYSTEM . 'functions.php';
require SYSTEM . 'init.php';
require SYSTEM . 'login.php';

if(!admin()) {
	http_response_code(500);
	die('You are not logged in. Probably session expired. Please login again.');
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

$settings = Settings::getInstance();

$success = $settings->save($_REQUEST['plugin'], $_POST['settings']);

$errors = $settings->getErrors();
if (count($errors) > 0) {
	http_response_code(500);
	die(implode('<br/>', $errors));
}

if ($success) {
	echo 'Saved at ' . date('H:i');
}
else {
	echo 'Something unexpected happened - it was impossible to save the settings, please try again later. If problem persists - contact MyAAC developers.';
}
