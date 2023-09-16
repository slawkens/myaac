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
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

require_once 'common.php';
require_once SYSTEM . 'functions.php';

$uri = $_SERVER['REQUEST_URI'];
if(false !== strpos($uri, 'index.php')) {
	$uri = str_replace_first('/index.php', '', $uri);
}

if(0 === strpos($uri, '/')) {
	$uri = str_replace_first('/', '', $uri);
}

if(preg_match("/^[A-Za-z0-9-_%'+\/]+\.png$/i", $uri)) {
	if (!empty(BASE_DIR)) {
		$tmp = explode('.', str_replace_first(str_replace_first('/', '', BASE_DIR) . '/', '', $uri));
	}
	else {
		$tmp = explode('.', $uri);
	}

	$_REQUEST['name'] = urldecode($tmp[0]);

	chdir(TOOLS . 'signature');
	include TOOLS . 'signature/index.php';
	exit();
}

if(preg_match("/^(.*)\.(gif|jpg|png|jpeg|tiff|bmp|css|js|less|map|html|zip|rar|gz|ttf|woff|ico)$/i", $_SERVER['REQUEST_URI'])) {
	http_response_code(404);
	exit;
}

if((!isset($config['installed']) || !$config['installed']) && file_exists(BASE . 'install'))
{
	header('Location: ' . BASE_URL . 'install/');
	throw new RuntimeException('Setup detected that <b>install/</b> directory exists. Please visit <a href="' . BASE_URL . 'install">this</a> url to start MyAAC Installation.<br/>Delete <b>install/</b> directory if you already installed MyAAC.<br/>Remember to REFRESH this page when you\'re done!');
}

$template_place_holders = array();

require_once SYSTEM . 'init.php';

// verify myaac tables exists in database
if(!$db->hasTable('myaac_account_actions')) {
	throw new RuntimeException('Seems that the table <strong>myaac_account_actions</strong> of MyAAC doesn\'t exist in the database. This is a fatal error. You can try to reinstall MyAAC by visiting <a href="' . BASE_URL . 'install">this</a> url.');
}

require_once SYSTEM . 'template.php';
require_once SYSTEM . 'login.php';
require_once SYSTEM . 'status.php';

$twig->addGlobal('config', $config);
$twig->addGlobal('status', $status);

require_once SYSTEM . 'router.php';

$hooks->trigger(HOOK_STARTUP);

// anonymous usage statistics
// sent only when user agrees
if(setting('core.anonymous_usage_statistics')) {
	$report_time = 30 * 24 * 60 * 60; // report one time per 30 days
	$should_report = true;

	$value = '';
	if($cache->enabled() && $cache->fetch('last_usage_report', $value)) {
		$should_report = time() > (int)$value + $report_time;
	}
	else {
		$value = '';
		if(fetchDatabaseConfig('last_usage_report', $value)) {
			$should_report = time() > (int)$value + $report_time;
			if($cache->enabled()) {
				$cache->set('last_usage_report', $value);
			}
		}
		else {
			registerDatabaseConfig('last_usage_report', time() - ($report_time - (7 * 24 * 60 * 60))); // first report after a week
			$should_report = false;
		}
	}

	if($should_report) {
		require_once LIBS . 'usage_statistics.php';
		Usage_Statistics::report();

		updateDatabaseConfig('last_usage_report', time());
		if($cache->enabled()) {
			$cache->set('last_usage_report', time());
		}
	}
}

if(setting('core.views_counter'))
	require_once SYSTEM . 'counter.php';

if(setting('core.visitors_counter')) {
	require_once SYSTEM . 'libs/visitors.php';
	$visitors = new Visitors(setting('core.visitors_counter_ttl'));
}

// backward support for gesior
if(setting('core.backward_support')) {
	define('INITIALIZED', true);
	$SQL = $db;
	$layout_header = template_header();
	$layout_name = $template_path;
	$news_content = '';
	$tickers_content = '';
	$main_content = '';

	$config['access_admin_panel'] = 2;
	$group_id_of_acc_logged = 0;
	if($logged && $account_logged)
		$group_id_of_acc_logged = $account_logged->getGroupId();

	$config['site'] = &$config;
	$config['server'] = &$config['lua'];
	$config['site']['shop_system'] = setting('core.gifts_system');
	$config['site']['gallery_page'] = true;

	if(!isset($config['vdarkborder']))
		$config['vdarkborder'] = '#505050';
	if(!isset($config['darkborder']))
		$config['darkborder'] = '#D4C0A1';
	if(!isset($config['lightborder']))
		$config['lightborder'] = '#F1E0C6';

	$config['site']['download_page'] = true;
	$config['site']['serverinfo_page'] = true;
	$config['site']['screenshot_page'] = true;

	$forumSetting = setting('core.forum');
	if($forumSetting != '')
		$config['forum_link'] = (strtolower($forumSetting) === 'site' ? getLink('forum') : $forumSetting);

	foreach($status as $key => $value)
		$config['status']['serverStatus_' . $key] = $value;
}

/**
 * @var OTS_Account $account_logged
 */
if ($logged && admin()) {
	$content .= $twig->render('admin-bar.html.twig', [
		'username' => USE_ACCOUNT_NAME ? $account_logged->getName() : $account_logged->getId()
	]);
}
$title_full =  (isset($title) ? $title . ' - ' : '') . $config['lua']['serverName'];
require $template_path . '/' . $template_index;

echo base64_decode('PCEtLSBQb3dlcmVkIGJ5IE15QUFDIDo6IGh0dHBzOi8vd3d3Lm15LWFhYy5vcmcvIC0tPg==') . PHP_EOL;
if(superAdmin()) {
	echo '<!-- Generated in: ' . round(microtime(true) - START_TIME, 4) . 'ms -->';
	echo PHP_EOL . '<!-- Queries done: ' . $db->queries() . ' -->';
	if(function_exists('memory_get_peak_usage')) {
		echo PHP_EOL . '<!-- Peak memory usage: ' . convert_bytes(memory_get_peak_usage(true)) . ' -->';
	}
}

$hooks->trigger(HOOK_FINISH);
