<?php
define('MYAAC_INSTALL', true);

use MyAAC\DataLoader;
use MyAAC\Models\FAQ as ModelsFAQ;
use MyAAC\Plugins;

require_once '../../common.php';

require SYSTEM . 'functions.php';
require BASE . 'install/includes/functions.php';
require BASE . 'install/includes/locale.php';

ini_set('max_execution_time', 300);

@ob_end_flush();
ob_implicit_flush();

header('X-Accel-Buffering: no');

if(isset($config['installed']) && $config['installed'] && !isset($_SESSION['saved'])) {
	warning($locale['already_installed']);
	return;
}

require SYSTEM . 'init.php';

// add player samples
require_once SYSTEM . 'migrations/49.php';
$up();

DataLoader::setLocale($locale);
DataLoader::load();

// add menus entries
require_once SYSTEM . 'migrations/17.php';
$up();

// update config.highscores_ids_hidden
require_once SYSTEM . 'migrations/20.php';
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

if(ModelsFAQ::count() == 0) {
	ModelsFAQ::create([
		'question' => 'What is this?',
		'answer' => 'This is website for OTS powered by MyAAC.',
	]);
}

$hooks->trigger(HOOK_INSTALL_FINISH);

$db->setClearCacheAfter(true);

// cleanup
foreach($_SESSION as $key => $value) {
	if(str_contains($key, 'var_')) {
		unset($_SESSION[$key]);
	}
}
unset($_SESSION['saved']);
if(file_exists(CACHE . 'install.txt')) {
	unlink(CACHE . 'install.txt');
}

$locale['step_finish_desc'] = str_replace('$ADMIN_PANEL$', generateLink(str_replace('tools/', '',ADMIN_URL), $locale['step_finish_admin_panel'], true), $locale['step_finish_desc']);
$locale['step_finish_desc'] = str_replace('$HOMEPAGE$', generateLink(str_replace('tools/', '', BASE_URL), $locale['step_finish_homepage'], true), $locale['step_finish_desc']);
$locale['step_finish_desc'] = str_replace('$LINK$', generateLink('https://my-aac.org', 'https://my-aac.org', true), $locale['step_finish_desc']);

success($locale['step_finish_desc']);
