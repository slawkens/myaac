<?php
/**
 * Initialize some defaults
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use DebugBar\StandardDebugBar;
use MyAAC\Cache\Cache;
use MyAAC\CsrfToken;
use MyAAC\Hooks;
use MyAAC\Models\Town;
use MyAAC\Settings;

defined('MYAAC') or die('Direct access not allowed!');

global $config;
if(!isset($config['installed']) || !$config['installed']) {
	throw new RuntimeException('MyAAC has not been installed yet or there was error during installation. Please install again.');
}

if(config('env') === 'dev') {
	require SYSTEM . 'exception.php';
}

if (config('env') === 'dev' || getBoolean(config('enable_debugbar'))) {
	$debugBar = new StandardDebugBar();
}

if(empty($config['server_path'])) {
	throw new RuntimeException('Server Path has been not set. Go to config.php and set it.');
}

// take care of trailing slash at the end
if($config['server_path'][strlen($config['server_path']) - 1] !== '/')
	$config['server_path'] .= '/';

// enable gzip compression if supported by the browser
if(isset($config['gzip_output']) && $config['gzip_output'] && isset($_SERVER['HTTP_ACCEPT_ENCODING']) && str_contains($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && function_exists('ob_gzhandler'))
	ob_start('ob_gzhandler');

// cache
global $cache;
$cache = Cache::getInstance();

// event system
global $hooks;
$hooks = new Hooks();
$hooks->load();

// twig
require_once SYSTEM . 'twig.php';

// action, used by many pages
$action = $_REQUEST['action'] ?? '';
define('ACTION', $action);

// errors, is also often used
$errors = [];

// trim values we receive
foreach($_POST as $var => $value) {
	if(is_string($value)) {
		$_POST[$var] = trim($value);
	}
}

foreach($_GET as $var => $value) {
	if(is_string($value))
		$_GET[$var] = trim($value);
}

foreach($_REQUEST as $var => $value) {
	if(is_string($value))
		$_REQUEST[$var] = trim($value);
}

// load otserv config file
$config_lua_reload = true;
if($cache->enabled()) {
	$tmp = null;
	if($cache->fetch('server_path', $tmp) && $tmp == $config['server_path']) {
		$tmp = null;
		if($cache->fetch('config_lua', $tmp) && $tmp) {
			$config['lua'] = unserialize($tmp);
			$config_lua_reload = false;
		}
	}
}

if($config_lua_reload) {
	$config['lua'] = load_config_lua($config['server_path'] . 'config.lua');

	// cache config
	if($cache->enabled()) {
		$cache->set('config_lua', serialize($config['lua']), 2 * 60);
		$cache->set('server_path', $config['server_path'], 10 * 60);
	}
}
unset($tmp);

if(isset($config['lua']['servername']))
	$config['lua']['serverName'] = $config['lua']['servername'];

if(isset($config['lua']['houserentperiod']))
	$config['lua']['houseRentPeriod'] = $config['lua']['houserentperiod'];

// localize data/ directory based on data directory set in config.lua
foreach(array('dataDirectory', 'data_directory', 'datadir') as $key) {
	if(!isset($config['lua'][$key][0])) {
		break;
	}

	$foundValue = $config['lua'][$key];
	if($foundValue[0] !== '/') {
		$foundValue = $config['server_path'] . $foundValue;
	}

	if($foundValue[strlen($foundValue) - 1] !== '/') {// do not forget about trailing slash
		$foundValue .= '/';
	}
}

if(!isset($foundValue)) {
	$foundValue = $config['server_path'] . 'data/';
}

$config['data_path'] = $foundValue;
unset($foundValue);

// POT
require_once SYSTEM . 'libs/pot/OTS.php';
$ots = POT::getInstance();
$eloquentConnection = null;
require_once SYSTEM . 'database.php';

// verify myaac tables exists in database
if(!defined('MYAAC_INSTALL') && !$db->hasTable('myaac_account_actions')) {
	throw new RuntimeException('Seems that the table myaac_account_actions of MyAAC doesn\'t exist in the database. This is a fatal error. You can try to reinstall MyAAC by visiting ' . (IS_CLI ? 'http://your-ip.com/' : BASE_URL) . 'install');
}

// execute migrations
$configDatabaseAutoMigrate = config('database_auto_migrate');
if (!isset($configDatabaseAutoMigrate) || $configDatabaseAutoMigrate) {
	require SYSTEM . 'migrate.php';
}

// settings
$settings = Settings::getInstance();
$settings->load();

// csrf protection
$token = getSession('csrf_token');
if (!isset($token) || !$token) {
	CsrfToken::generate();
}

// deprecated config values
require_once SYSTEM . 'compat/config.php';

// deprecated classes
require_once SYSTEM . 'compat/classes.php';

date_default_timezone_set(setting('core.date_timezone'));

setting(
	[
		'core.account_mail_verify',
		setting('core.account_mail_verify') && setting('core.mail_enabled')
	]
);

$settingsItemImagesURL = setting('core.item_images_url');
if($settingsItemImagesURL[strlen($settingsItemImagesURL) - 1] !== '/') {
	setting(['core.item_images_url', $settingsItemImagesURL . '/']);
}

define('USE_ACCOUNT_NAME', $db->hasColumn('accounts', 'name'));
define('USE_ACCOUNT_NUMBER', $db->hasColumn('accounts', 'number'));
define('USE_ACCOUNT_SALT', $db->hasColumn('accounts', 'salt'));

$towns = Cache::remember('towns', 10 * 60, function () use ($db) {
	if ($db->hasTable('towns') && Town::count() > 0) {
		return Town::orderBy('id', 'ASC')->pluck('name', 'id')->toArray();
	}

	return [];
});

if (count($towns) <= 0) {
	$towns = setting('core.towns');
}

config(['towns', $towns]);
unset($towns);
