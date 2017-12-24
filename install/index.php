<?php
require('../common.php');

// includes
require(SYSTEM . 'functions.php');
require(BASE . 'install/includes/functions.php');
require(BASE . 'install/includes/locale.php');

if(file_exists(BASE . 'config.local.php'))
	require(BASE . 'config.local.php');

// twig
require_once LIBS . 'Twig/Autoloader.php';
Twig_Autoloader::register();

$twig_loader = new Twig_Loader_Filesystem(SYSTEM . 'templates');
$twig = new Twig_Environment($twig_loader, array(
	'cache' => CACHE . 'twig/',
	'auto_reload' => true
));

if(isset($_POST['vars']))
{
	foreach($_POST['vars'] as $key => $value)
		$_SESSION['var_' . $key] = $value;
}

// step
$step = isset($_POST['step']) ? $_POST['step'] : 'welcome';

$steps = array(1 => 'welcome', 2 => 'license', 3 => 'requirements', 4 => 'config', 5 => 'database', 6 => 'admin', 7 => 'finish');
if(!in_array($step, $steps)) // check if step is valid
	die('ERROR: Unknown step.');

$errors = array();
if($step == 'database') {
	foreach($_SESSION as $key => $value) {
		if(strpos($key, 'var_') === false || strpos($key, 'account') !== false || strpos($key, 'password') !== false) {
			continue;
		}

		$key = str_replace('var_', '', $key);
		if($key != 'usage' && empty($value))
		{
			$errors[] = $locale['please_fill_all'];
			break;
		}
		else if($key == 'server_path')
		{
			$config['server_path'] = $value;

			// take care of trailing slash at the end
			if($config['server_path'][strlen($config['server_path']) - 1] != '/')
				$config['server_path'] .= '/';

			if(!file_exists($config['server_path'] . 'config.lua')) {
				$errors[] = $locale['step_database_error_config'];
				break;
			}
		}
		else if($key == 'mail_admin' && !Validator::email($value))
		{
			$errors[] = $locale['step_config_mail_admin_error'];
			break;
		}
		else if($key == 'mail_address' && !Validator::email($value))
		{
			$errors[] = $locale['step_config_mail_address_error'];
			break;
		}
	}

	if(!empty($errors)) {
		$step = 'config';
	}
}
else if($step == 'admin') {
	$config_failed = true;
	if(file_exists(BASE . 'config.local.php') && isset($config['installed']) && $config['installed'] && isset($_SESSION['saved'])) {
		$config_failed = false;
	}

	if($config_failed) {
		$step = 'database';
	}
}
else if($step == 'finish') {
	// password
	$password = $_SESSION['var_password'];

	if(isset($_SESSION['var_account'])) {
		if(!Validator::accountName($_SESSION['var_account'])) {
			$errors[] = $locale['step_admin_account_error_format'];
		}
		else if(strtoupper($_SESSION['var_account']) == strtoupper($password)) {
			$errors[] = $locale['step_admin_account_error_same'];
		}
	}
	else if(isset($_SESSION['var_account_id'])) {
		if(!Validator::accountId($_SESSION['var_account_id'])) {
			$errors[] = $locale['step_admin_account_id_error_format'];
		}
		else if($_SESSION['var_account_id'] == $password) {
			$errors[] = $locale['step_admin_account_id_error_same'];
		}
	}

	if(empty($password)) {
		$errors[] = $locale['step_admin_password_error_empty'];
	}
	else if(!Validator::password($password)) {
		$errors[] = $locale['step_admin_password_error_format'];
	}

	if(!empty($errors)) {
		$step = 'admin';
	}
}

$error = false;

clearstatcache();
if(is_writable(CACHE) && (MYAAC_OS != 'WINDOWS' || win_is_writable(CACHE))) {
	ob_start();

	$step_id = array_search($step, $steps);
	require('steps/' . $step_id . '-' . $step . '.php');
	$content = ob_get_contents();
	ob_end_clean();
}
else {
	$content = error(file_get_contents(BASE . 'install/includes/twig_error.html'), true);
}

// render
require('template/template.php');
//$_SESSION['laststep'] = $step;