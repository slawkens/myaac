<?php
require('../common.php');

// step
$step = isset($_POST['step']) ? $_POST['step'] : 'welcome';

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

$error = false;

// step include
ob_start();
require('steps/' . $step . '.php');
$content = ob_get_contents();
ob_end_clean();

// render
require('template/template.php');
//$_SESSION['laststep'] = $step;

?>
