<?php

// few things we'll need
require '../common.php';

const ADMIN_PANEL = true;
const MYAAC_ADMIN = true;

if(file_exists(BASE . 'install') && (!isset($config['installed']) || !$config['installed']))
{
	header('Location: ' . BASE_URL . 'install/');
	throw new RuntimeException('Setup detected that <b>install/</b> directory exists. Please visit <a href="' . BASE_URL . 'install">this</a> url to start MyAAC Installation.<br/>Delete <b>install/</b> directory if you already installed MyAAC.<br/>Remember to REFRESH this page when you\'re done!');
}

$content = '';

// validate page
$page = $_GET['p'] ?? '';
if(empty($page) || preg_match("/[^a-zA-Z0-9_\-\/.]/", $page))
	$page = 'dashboard';

$page = strtolower($page);
define('PAGE', $page);

require SYSTEM . 'functions.php';
require SYSTEM . 'init.php';

require __DIR__ . '/includes/debugbar.php';
require SYSTEM . 'status.php';
require SYSTEM . 'login.php';
require __DIR__ . '/includes/functions.php';

$twig->addGlobal('config', $config);
$twig->addGlobal('status', $status);

if (ACTION == 'logout') {
	require SYSTEM . 'logout.php';
}

// if we're not logged in - show login box
if(!$logged || !admin()) {
	$page = 'login';
}

// include our page
$file = __DIR__ . '/pages/' . $page . '.php';
if(!@file_exists($file)) {
	if (str_contains($page, 'plugins/')) {
		$file = BASE . $page;
	}
	else {
		$page = '404';
		$file = SYSTEM . 'pages/404.php';
	}
}

ob_start();
if($hooks->trigger(HOOK_ADMIN_BEFORE_PAGE)) {
	require $file;
}

$content .= ob_get_contents();
ob_end_clean();

// template
$template_path = 'template/';
require __DIR__ . '/' . $template_path . 'template.php';
