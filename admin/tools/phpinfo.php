<?php

use MyAAC\Services\LoginService;

define('MYAAC_ADMIN', true);

require '../../common.php';
require SYSTEM . 'functions.php';
require SYSTEM . 'init.php';

$loginService = new LoginService();
$loginService->checkLogin();

if(!admin()) {
	die('Access denied.');
}

if(!function_exists('phpinfo')) {
	die('phpinfo() disabled on this web server.');
}

phpinfo();
