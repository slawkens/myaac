<?php
/**
 * Initialize some defaults
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

// load configuration
require_once BASE . 'config.php';
if(file_exists(BASE . 'config.local.php')) // user customizations
	require BASE . 'config.local.php';

if(!isset($config['installed']) || !$config['installed']) {
	throw new RuntimeException('MyAAC has not been installed yet or there was error during installation. Please install again.');
}

date_default_timezone_set($config['date_timezone']);

// enable gzip compression if supported by the browser
if($config['gzip_output'] && isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false && function_exists('ob_gzhandler'))
	ob_start('ob_gzhandler');

// cache
require_once SYSTEM . 'libs/cache.php';
$cache = Cache::getInstance();

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

if(isset($config['servername']))
	$config['serverName'] = $config['servername'];

if(isset($config['houserentperiod']))
	$config['houseRentPeriod'] = $config['houserentperiod'];

if($config['item_images_url'][strlen($config['item_images_url']) - 1] !== '/')
	$config['item_images_url'] .= '/';

// new config values for compatibility
if(!isset($config['highscores_ids_hidden']) || count($config['highscores_ids_hidden']) == 0) {
	$config['highscores_ids_hidden'] = array(0);
}

$config['account_mail_verify'] = config('account_mail_verify') && config('mail_enabled');

// POT
require_once SYSTEM . 'libs/pot/OTS.php';
$ots = POT::getInstance();
require_once SYSTEM . 'database.php';

// twig
require_once SYSTEM . 'twig.php';

define('USE_ACCOUNT_NAME', $db->hasColumn('accounts', 'name'));
// load vocation names
$tmp = '';
if($cache->enabled() && $cache->fetch('vocations', $tmp)) {
	$config['vocations'] = unserialize($tmp);
}
else {
	if(!class_exists('DOMDocument')) {
		throw new RuntimeException('Please install PHP xml extension. MyAAC will not work without it.');
	}

	$vocations = new DOMDocument();

	$file = $config['data_path'] . 'XML/vocations.xml';

	if(!$vocations->load($file))
		throw new RuntimeException('ERROR: Cannot load <i>vocations.xml</i> - the file is malformed. Check the file with xml syntax validator.');

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

////////////////////////////////////////
// load towns from database (TFS 1.3) //
////////////////////////////////////////

$tmp = '';
$towns = [];
if($cache->enabled() && $cache->fetch('towns', $tmp)) {
	$towns = unserialize($tmp);
}
else {
	if($db->hasTable('towns')) {
		$query = $db->query('SELECT `id`, `name` FROM `towns`;')->fetchAll(PDO::FETCH_ASSOC);

		foreach($query as $town) {
			$towns[$town['id']] = $town['name'];
		}

		unset($query);
	}
	else {
		$towns = config('towns');
	}

	if($cache->enabled()) {
		$cache->set('towns', serialize($towns), 600);
	}
}

config(['towns', $towns]);
//////////////////////////////////////////////
// END - load towns from database (TFS 1.3) //
//////////////////////////////////////////////
