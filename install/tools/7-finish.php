<?php
define('MYAAC_INSTALL', true);

use MyAAC\DataLoader;

require_once '../../common.php';

require SYSTEM . 'functions.php';
require BASE . 'install/includes/functions.php';
require BASE . 'install/includes/locale.php';

ini_set('max_execution_time', 300);

@ob_end_flush();
ob_implicit_flush();

if(file_exists(BASE . 'install/install.lock')) {
	warning($locale['already_installed']);
	return;
}

header('X-Accel-Buffering: no');

require SYSTEM . 'init.php';

// add player samples
require_once SYSTEM . 'migrations/49.php';
$up();

DataLoader::setLocale($locale);
DataLoader::load();

clearCache();

// add menus entries
require_once SYSTEM . 'migrations/17.php';
$up();

// add z_polls tables
require_once SYSTEM . 'migrations/22.php';
$up();

// add myaac_pages pages
require_once SYSTEM . 'migrations/27.php';
$up();
require_once SYSTEM . 'migrations/30.php';
$up();

// new monster columns
require_once SYSTEM . 'migrations/31.php';
$up();

// rules page
require_once SYSTEM . 'migrations/45.php';
$up();

$hooks->trigger(HOOK_INSTALL_FINISH);

$db->setClearCacheAfter(true);

// cleanup
foreach($_SESSION as $key => $value) {
	if(str_contains($key, 'var_')) {
		unset($_SESSION[$key]);
	}
}

if(file_exists(CACHE . 'install.txt')) {
	unlink(CACHE . 'install.txt');
}

$successOne = true;
if(file_exists(BASE . 'install/ip.txt')) {
	$successOne = unlink(BASE . 'install/ip.txt');
}

$successTwo = file_put_contents(BASE . 'install/install.lock',
	'This file is used to prevent the installation process from running again. You can delete it if you want to run the installer again.'
);

if (!$successOne && !$successTwo) {
	error($locale['step_finish_fatal_error']);
}

$locale['step_finish_desc'] = str_replace('$ADMIN_PANEL$', generateLink(str_replace('tools/', '',ADMIN_URL), $locale['step_finish_admin_panel'], true), $locale['step_finish_desc']);
$locale['step_finish_desc'] = str_replace('$HOMEPAGE$', generateLink(str_replace('tools/', '', BASE_URL), $locale['step_finish_homepage'], true), $locale['step_finish_desc']);
$locale['step_finish_desc'] = str_replace('$LINK$', generateLink('https://my-aac.org', 'https://my-aac.org', true), $locale['step_finish_desc']);

success($locale['step_finish_desc']);
