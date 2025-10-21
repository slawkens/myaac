<?php
const MYAAC_ADMIN = true;
const IGNORE_SET_LAST_VISIT = true;

require '../../common.php';
require SYSTEM . 'functions.php';
require SYSTEM . 'init.php';
require SYSTEM . 'login.php';

if(!admin())
	die('Access denied.');

if(!function_exists('phpinfo'))
	die('phpinfo() disabled on this web server.');

phpinfo();
