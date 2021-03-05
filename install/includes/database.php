<?php
defined('MYAAC') or die('Direct access not allowed!');

require SYSTEM . 'libs/pot/OTS.php';
$ots = POT::getInstance();
require SYSTEM . 'database.php';

if(!isset($db)) {
	$database_error = '<p class="lead">' . $locale['step_database_error_mysql_connect'] . '</p>';
	
	$database_error .= '<p>' . $locale['step_database_error_mysql_connect_2'] . '</p>';

	$database_error .= '<ul class="list-group">' .
							'<li class="list-group-item list-group-item-warning">' . $locale['step_database_error_mysql_connect_3'] . '</li>' .
							'<li class="list-group-item list-group-item-warning">' . $locale['step_database_error_mysql_connect_4'] . '</li>' .
						'</ul>';

	$database_error .= '<div class="alert alert-danger mt-4">
							<span>' . $error . '</span>
						</div>';
}
else {
	if($db->hasTable('accounts'))
		define('USE_ACCOUNT_NAME', $db->hasColumn('accounts', 'name'));
}

if(!defined('USE_ACCOUNT_NAME')) {
	define('USE_ACCOUNT_NAME', false);
}
?>