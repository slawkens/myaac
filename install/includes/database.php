<?php
defined('MYAAC') or die('Direct access not allowed!');

require(SYSTEM . 'libs/pot/OTS.php');
$ots = POT::getInstance();
require(SYSTEM . 'database.php');

if($db->hasTable('accounts'))
	define('USE_ACCOUNT_NAME', $db->hasColumn('accounts', 'name'));
else
	define('USE_ACCOUNT_NAME', false);
?>