<?php
/**
 * Initialize some defaults
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\CsrfToken;

defined('MYAAC') or die('Direct access not allowed!');

if(!isset($config['installed']) || !$config['installed']) {
	throw new RuntimeException('MyAAC has not been installed yet or there was error during installation. Please install again.');
}

use DebugBar\StandardDebugBar;

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
if(isset($config['gzip_output']) && $config['gzip_output'] && isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false && function_exists('ob_gzhandler'))
	ob_start('ob_gzhandler');

// cache
require_once SYSTEM . 'libs/cache.php';
$cache = Cache::getInstance();

// event system
require_once SYSTEM . 'hooks.php';
$hooks = new Hooks();
$hooks->load();

// twig
require_once SYSTEM . 'twig.php';

// action, used by many pages
$action = $_REQUEST['action'] ?? '';
define('ACTION', $action);

// trim values we receive
if(isset($_POST))
{
	foreach($_POST as $var => $value) {
		if(is_string($value)) {
			$_POST[$var] = trim($value);
		}
	}
}
if(isset($_GET))
{
	foreach($_GET as $var => $value) {
		if(is_string($value))
			$_GET[$var] = trim($value);
	}
}
if(isset($_REQUEST))
{
	foreach($_REQUEST as $var => $value) {
		if(is_string($value))
			$_REQUEST[$var] = trim($value);
	}
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
		$cache->set('config_lua', serialize($config['lua']), 120);
		$cache->set('server_path', $config['server_path']);
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

// execute migrations
require SYSTEM . 'migrate.php';

// settings
require_once LIBS . 'Settings.php';
$settings = Settings::getInstance();
$settings->load();

// csrf protection
$token = getSession('csrf_token');
if (!isset($token) || !$token) {
	CsrfToken::generate();
}

// deprecated config values
require_once SYSTEM . 'compat/config.php';

date_default_timezone_set(setting('core.date_timezone'));

setting(
	[
		'core.account_create_character_create',
		setting('core.account_create_character_create') && (!setting('core.mail_enabled') || !setting('core.account_mail_verify'))
	]
);

$settingsItemImagesURL = setting('core.item_images_url');
if($settingsItemImagesURL[strlen($settingsItemImagesURL) - 1] !== '/') {
	setting(['core.item_images_url', $settingsItemImagesURL . '/']);
}

define('USE_ACCOUNT_NAME', $db->hasColumn('accounts', 'name'));
define('USE_ACCOUNT_NUMBER', $db->hasColumn('accounts', 'number'));
define('USE_ACCOUNT_SALT', $db->hasColumn('accounts', 'salt'));

require LIBS . 'Towns.php';
Towns::load();
