<?php
/**
 * Initialize some defaults
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

// load configuration
require_once BASE . 'config.php';
if(file_exists(BASE . 'config.local.php')) // user customizations
	require BASE . 'config.local.php';

if(!isset($config['installed']) || !$config['installed']) {
	die('MyAAC has not been installed yet or there was error during installation. Please install again.');
}

date_default_timezone_set($config['date_timezone']);
// take care of trailing slash at the end
if($config['server_path'][strlen($config['server_path']) - 1] !== '/')
	$config['server_path'] .= '/';

// enable gzip compression if supported by the browser
if($config['gzip_output'] && isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false && function_exists('ob_gzhandler'))
	ob_start('ob_gzhandler');

// cache
require_once SYSTEM . 'libs/cache.php';
$cache = Cache::getInstance($config['cache_engine'], $config['cache_prefix']);

// twig
require_once LIBS . 'Twig/Autoloader.php';
Twig_Autoloader::register();

$twig_loader = new Twig_Loader_Filesystem(SYSTEM . 'templates');
$twig = new Twig_Environment($twig_loader, array(
	'cache' => CACHE . 'twig/',
	'auto_reload' => true,
	//'debug' => true
));

$function = new Twig_SimpleFunction('getStyle', function ($i) {
	return getStyle($i);
});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('getLink', function ($s) {
	return getLink($s);
});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('hook', function ($hook) {
	global $hooks;
	$hooks->trigger($hook);
});
$twig->addFunction($function);

$filter = new Twig_SimpleFilter('urlencode', function ($s) {
	return urlencode($s);
});
$twig->addFilter($filter);

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

if($config['item_images_url'][strlen($config['item_images_url']) - 1] !== '/')
	$config['item_images_url'] .= '/';

// localize data/ directory
if(isset($config['lua']['dataDirectory'][0]))
{
	$tmp = $config['lua']['dataDirectory'];
	if($tmp[0] !== '/')
		$tmp = $config['server_path'] . $tmp;

	if($tmp[strlen($tmp) - 1] !== '/') // do not forget about trailing slash
		$tmp .= '/';
}
else if(isset($config['lua']['data_directory'][0]))
{
	$tmp = $config['lua']['data_directory'];
	if($tmp[0] !== '/')
		$tmp = $config['server_path'] . $tmp;

	if($tmp[strlen($tmp) - 1] !== '/') // do not forget about trailing slash
		$tmp .= '/';
}
else if(isset($config['lua']['datadir'][0]))
{
	$tmp = $config['lua']['datadir'];
	if($tmp[0] !== '/')
		$tmp = $config['server_path'] . $tmp;

	if($tmp[strlen($tmp) - 1] !== '/') // do not forget about trailing slash
		$tmp .= '/';
}
else
	$tmp = $config['server_path'] . 'data/';

$config['data_path'] = $tmp;
unset($tmp);

// new config values for compability
if(!isset($config['highscores_ids_hidden']) || count($config['highscores_ids_hidden']) == 0) {
	$config['highscores_ids_hidden'] = array(0);
}

// POT
require_once(SYSTEM . 'libs/pot/OTS.php');
$ots = POT::getInstance();
require_once(SYSTEM . 'database.php');

define('USE_ACCOUNT_NAME', $db->hasColumn('accounts', 'name'));
// load vocation names
$tmp = '';
if($cache->enabled() && $cache->fetch('vocations', $tmp)) {
	$config['vocations'] = unserialize($tmp);
}
else {
	$vocations = new DOMDocument();
	$file = $config['data_path'] . 'XML/vocations.xml';
	if(!@file_exists($file))
		$file = $config['data_path'] . 'vocations.xml';

	$vocations->load($file);

	if(!$vocations)
		die('ERROR: Cannot load <i>vocations.xml</i> file.');

	$config['vocations'] = array();
	foreach($vocations->getElementsByTagName('vocation') as $vocation) {
		$id = $vocation->getAttribute('id');
		$config['vocations'][$id] = $vocation->getAttribute('name');
	}

	if($cache->enabled()) {
		$cache->set('vocations', serialize($config['vocations']), 120);
	}
}
unset($tmp, $id, $vocation);

// load towns
/* TODO: doesnt work
ini_set('memory_limit', '-1');
$tmp = '';

if($cache->enabled() && $cache->fetch('towns', $tmp)) {
	$config['towns'] = unserialize($tmp);
}
else {
	$towns = new OTS_OTBMFile();
	$towns->loadFile('D:/Projekty/opentibia/wodzislawski/data/world/wodzislawski.otbm');

	$config['towns'] = $towns->getTownsList();
	if($cache->enabled()) {
		$cache->set('towns', serialize($config['towns']), 120);
	}
}
*/
?>
