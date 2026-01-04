<?php

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

if (!isset($_POST['collapse'])) {
	http_response_code(500);
	die('Something went wrong.');
}

csrfProtect();

setSession('admin.menu-collapse', $_POST['collapse'] == 'true');
