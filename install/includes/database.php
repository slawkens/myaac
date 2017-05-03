<?php
defined('MYAAC') or die('Direct access not allowed!');

require(SYSTEM . 'libs/pot/OTS.php');
$ots = POT::getInstance();
require(SYSTEM . 'database.php');

if(tableExist('accounts'))
	define('USE_ACCOUNT_NAME', fieldExist('name', 'accounts'));
else
	define('USE_ACCOUNT_NAME', false);
?>