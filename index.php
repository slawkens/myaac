<?php
/**
 * Project: MyAAC
 *     Automatic Account Creator for Open Tibia Servers
 * File: index.php
 *
 * This is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.1.2
 * @link      http://my-aac.org
 */

// uncomment if your php.ini have display_errors disabled and you want to see errors
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once('common.php');
require_once(BASE . 'config.local.php');

if(file_exists(BASE . 'install') && (!isset($config['installed']) || !$config['installed']))
{
	header('Location: ' . BASE_URL . 'install/');
	die('Setup detected that <b>install/</b> directory exists. Please visit <a href="' . BASE_URL . 'install">this</a> url to start MyAAC Installation.<br/>Delete <b>install/</b> directory if you already installed MyAAC.<br/>Remember to REFRESH this page when you\'re done!');
}

// define page visited, so it can be used within events system
$page = isset($_REQUEST['subtopic']) ? $_REQUEST['subtopic'] : (isset($_GET['p']) ? $_GET['p'] : '');
if(empty($page) || preg_match('/[^A-z0-9_\-]/', $page))
	$page = 'news';

$page = strtolower($page);
define('PAGE', $page);

$template_place_holders = array();

require_once(SYSTEM . 'functions.php');
require_once(SYSTEM . 'init.php');
require_once(SYSTEM . 'login.php');
require_once(SYSTEM . 'status.php');
require_once(SYSTEM . 'template.php');

// database migrations
$tmp = '';
if(fetchDatabaseConfig('database_version', $tmp)) { // we got version
	$tmp = (int)$tmp;
	if($tmp < DATABASE_VERSION) { // import if older
		for($i = $tmp + 1; $i <= DATABASE_VERSION; $i++) {
			require(SYSTEM . 'migrations/' . $i . '.php');
		}
		
		updateDatabaseConfig('database_version', DATABASE_VERSION);
	}
}
else { // register first version
	require(SYSTEM . 'migrations/1.php');
	registerDatabaseConfig('database_version', 1);
}

// event system
require_once(SYSTEM . 'hooks.php');
$hooks = new Hooks();
$hooks->load();
$hooks->trigger(HOOK_STARTUP);

if($config['views_counter'])
	require_once(SYSTEM . 'counter.php');

if($config['visitors_counter'])
{
	require_once(SYSTEM . 'libs/visitors.php');
	$visitors = new Visitors($config['visitors_counter_ttl']);
}

// page content loading
if(!isset($content[0]))
	$content = '';
$load_it = true;

// check if site has been closed
if($config['site_closed'])
{
	if(!admin())
	{
		$title = $config['site_closed_title'];
		$content .= $config['site_closed_message'];
		$load_it = false;
	}

	if(!$logged)
	{
		ob_start();
		require(SYSTEM . 'pages/accountmanagement.php');
		$content .= ob_get_contents();
		ob_end_clean();
		$load_it = false;
	}
}

// backward support for gesior
if($config['backward_support']) {
	define('INITIALIZED', true);
	$SQL = $db;
	$layout_header = template_header();
	$layout_name = $template_path;
	$news_content = '';
	$subtopic = PAGE;
	$main_content = '';
	
	$config['access_admin_panel'] = 2;
	$group_id_of_acc_logged = 0;
	if($logged && $account_logged)
		$group_id_of_acc_logged = $account_logged->getGroupId();

	$config['site'] = &$config;
	$config['server'] = &$config['lua'];
	$config['site']['shop_system'] = $config['gifts_system'];

	if(!isset($config['vdarkborder']))
		$config['vdarkborder'] = '#505050';
	if(!isset($config['darkborder']))
		$config['darkborder'] = '#D4C0A1';
	if(!isset($config['lightborder']))
		$config['lightborder'] = '#F1E0C6';

	$config['site']['download_page'] = false;
	$config['site']['serverinfo_page'] = true;
	$config['site']['screenshot_page'] = true;

	if($config['forum'] != '')
		$config['forum_link'] = (strtolower($config['forum']) == 'site' ? internalLayoutLink('forum') : $config['forum']);

	foreach($status as $key => $value)
		$config['status']['serverStatus_' . $key] = $value;
}

if($load_it)
{
	if($config['site_closed'] && admin())
		$content .= '<p class="note">Site is under maintenance (closed mode). Only privileged users can see it.</p>';

	if($config['backward_support'])
		require(SYSTEM . 'compat_pages.php');

	$ignore = false;
	$file = SYSTEM . 'pages/' . $page . '.php';
	if(!@file_exists($file))
	{
		$logged_access = 0;
		if($logged && $account_logged && $account_logged->isLoaded()) {
			$logged_access = $account_logged->getAccess();
		}

		$query =
			$db->query(
				'SELECT `title`, `body`, `php`' .
				' FROM `' . TABLE_PREFIX . 'pages`' .
				' WHERE `name` LIKE ' . $db->quote($page) . ' AND `hidden` != 1 AND `access` <= ' . $db->quote($logged_access));
		if($query->rowCount() > 0) // found page
		{
			$ignore = true;
			$query = $query->fetch();
			$title = $query['title'];

			if($query['php'] == '1') // execute it as php code
			{
				$tmp = substr($query['body'], 0, 10);
				if(($pos = strpos($tmp, '<?php')) !== false) {
					$tmp = preg_replace('/<\?php/', '', $query['body'], 1);
				}
				else if(($pos = strpos($tmp, '<?')) !== false) {
					$tmp = preg_replace('/<\?/', '', $query['body'], 1);
				}
				else
					$tmp = $query['body'];

				$php_errors = array();
				function error_handler($errno, $errstr) {
					global $php_errors;
					$php_errors[] = array('errno' => $errno, 'errstr' => $errstr);
				}
				set_error_handler('error_handler');

				ob_start();
				eval($tmp);
				$content .= ob_get_contents();
				ob_end_clean();

				restore_error_handler();
				if(isset($php_errors[0]) && superAdmin()) {
					var_dump($php_errors);
				}
			}
			else
				$content .= $query['body']; // plain html
		}
		else
		{
			$page = '404';
			$file = SYSTEM . 'pages/404.php';
		}
	}

	ob_start();
	$hooks->trigger(HOOK_BEFORE_PAGE);

	if(!$ignore)
		require($file);

	if($config['backward_support'] && isset($main_content[0]))
		$content .= $main_content;

	$content .= ob_get_contents();
	ob_end_clean();
	$hooks->trigger(HOOK_AFTER_PAGE);
}

if($config['backward_support']) {
	$main_content = $content;
	if(!isset($title))
		$title = ucfirst($page);

	$topic = $title;
}

$title_full =  (isset($title) ? $title . $config['title_separator'] : '') . $config['lua']['serverName'];
if(file_exists($template_path . '/index.php'))
	require($template_path . '/index.php');
else if(file_exists($template_path . '/template.php')) // deprecated
	require($template_path . '/template.php');
else if($config['backward_support'] && file_exists($template_path . '/layout.php'))
{
	require($template_path . '/layout.php');
}
else
{
	// TODO: save more info to log file
	die('ERROR: Cannot load template.');
}

echo '<!-- MyAAC ' . MYAAC_VERSION . ' :: http://www.my-aac.org/ -->' . "\n";
if(($config['debug_level'] & 1) == 1)
	echo '<!-- Generated in :: ' . round(microtime(true) - START_TIME, 4) . ' -->';

if(($config['debug_level'] & 2) == 2)
	echo "\n" . '<!-- Queries done :: ' . $db->queries() . ' -->';

if(($config['debug_level'] & 4) == 4 && function_exists('memory_get_peak_usage'))
	echo "\n" . '<!-- Peak memory usage: ' . convert_bytes(memory_get_peak_usage(true)) . ' -->';

$hooks->trigger(HOOK_FINISH);
?>
