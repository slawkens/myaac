<?php
/**
 * Template parsing engine.
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.6.6
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

// template
$template_name = $config['template'];
if($config['template_allow_change'])
{
	if(isset($_GET['template']))
	{
		$template_name = $_GET['template'];
		if(!preg_match("/[^A-z0-9_\-]/", $template_name)) { // validate template
			//setcookie('template', $template_name, 0, BASE_DIR . '/', $_SERVER["SERVER_NAME"]);
			setSession('template', $template_name);
			header('Location:' . getSession('last_uri'));
		}
		else
			$template_name = $config['template'];
	}
	else {
		$template_session = getSession('template');
		if ($template_session !== false) {
			if (!preg_match("/[^A-z0-9_\-]/", $template_session)) {
				$template_name = $template_session;
			} else {
				$template_name = $config['template'];
			}
		}
	}
}
$template_path = 'templates/' . $template_name;

if(!file_exists($template_path . '/index.php') &&
	!file_exists($template_path . '/template.php') &&
	!file_exists($template_path . '/layout.php'))
{
	$template_name = 'kathrine';
	$template_path = TEMPLATES . $template_name;
}

$file = $template_path . '/config.ini';
$exists = file_exists($file);
if($exists || ($config['backward_support'] && file_exists($template_path . '/layout_config.ini')))
{
	if(!$exists)
		$file = $template_path . '/layout_config.ini';

	if($cache->enabled())
	{
		$tmp = '';
		if($cache->fetch('template_ini_' . $template_name, $tmp))
				$template_ini = unserialize($tmp);
		else
		{
			$template_ini = parse_ini_file($file);
			$cache->set('template_ini_' . $template_name, serialize($template_ini));
		}
	}
	else
		$template_ini = parse_ini_file($file);

	foreach($template_ini as $key => $value)
		$config[$key] = $value;
}
else if(file_exists($template_path . '/config.php'))
	require($template_path . '/config.php');

$template = array();
$template['link_account_manage'] = getLink('account/manage');
$template['link_account_create'] = getLink('account/create');
$template['link_account_lost'] = getLink('account/lost');
$template['link_account_logout'] = getLink('account/logout');

$template['link_news_archive'] = getLink('news/archive');

$links = array('news', 'changelog', 'rules', 'downloads', 'characters', 'online', 'highscores', 'powergamers', 'lastkills', 'houses', 'guilds', 'wars', 'polls', 'bans', 'team', 'creatures', 'spells', 'commands', 'experienceStages', 'freeHouses', 'serverInfo', 'experienceTable', 'faq', 'points', 'gifts', 'bugtracker', 'gallery');
foreach($links as $link) {
	$template['link_' . $link] = getLink($link);
}

$template['link_screenshots'] = getLink('gallery');
$template['link_movies'] = getLink('videos');

$template['link_gifts_history'] = getLink('gifts', 'history');
if($config['forum'] != '')
{
	if(strtolower($config['forum']) == 'site')
		$template['link_forum'] = "<a href='" . getLink('forum') . "'>";
	else
		$template['link_forum'] = "<a href='" . $config['forum'] . "' target='_blank'>";
}

$twig->addGlobal('template_path', $template_path);
if($twig_loader && file_exists(BASE . $template_path))
	$twig_loader->prependPath(BASE . $template_path);

function get_template_menus() {
	global $db, $template_name;
	
	$menus = array();
	$query = $db->query('SELECT `name`, `link`, `category` FROM `' . TABLE_PREFIX . 'menu` WHERE `template` = ' . $db->quote($template_name) . ' ORDER BY `category`, `ordering` ASC');
	foreach($query->fetchAll() as $menu) {
		$menus[$menu['category']][] = array('name' => $menu['name'], 'link' => $menu['link']);
	}
	
	return $menus;
}
?>
