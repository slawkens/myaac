<?php
require('../common.php');

// includes
require(SYSTEM . 'functions.php');
require(BASE . 'install/includes/functions.php');
require(BASE . 'install/includes/locale.php');
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
if($step == 'database')
{
	foreach($_POST['vars'] as $key => $value)
	{
		if($key != 'usage' && empty($value))
		{
			$errors[] = $locale['please_fill_all'];
			break;
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

// step include
ob_start();

$step_id = array_search($step, $steps);
require('steps/' . $step_id . '-' . $step . '.php');
$content = ob_get_contents();
ob_end_clean();

// render
require('template/template.php');
//$_SESSION['laststep'] = $step;

?>
