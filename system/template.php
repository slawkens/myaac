<?php
/**
 * Template parsing engine.
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.3.0
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
			$_SESSION['template'] = $template_name;
		}
		else
			$template_name = $config['template'];
	}
	else if(isset($_SESSION['template']))
	{
		if(!preg_match("/[^A-z0-9_\-]/", $_SESSION['template'])) {
			$template_name = $_SESSION['template'];
		}
		else {
			$template_name = $config['template'];
		}
	}
}
$template_path = 'templates/' . $template_name;

if(!file_exists($template_path . '/index.php') &&
	!file_exists($template_path . '/template.php') &&
	!file_exists($template_path . '/layout.php'))
{
	$template_name = 'kathrine';
	$template_path = 'templates/' . $template_name;
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
$template['link_account_manage'] = internalLayoutLink('account' . ($config['friendly_urls'] ? '/manage' : 'management'));
$template['link_account_create'] = internalLayoutLink(($config['friendly_urls'] ? 'account/create' : 'createaccount'));
$template['link_account_lost'] = internalLayoutLink(($config['friendly_urls'] ? 'account/lost' : 'lostaccount'));
$template['link_account_logout'] = internalLayoutLink(($config['friendly_urls'] ? 'account' : 'accountmanagement'), 'logout');

$template['link_news_archive'] = internalLayoutLink('news' . ($config['friendly_urls'] ? '/' : '') . 'archive');

$links = array('news', 'changelog', 'rules', 'downloads', 'characters', 'online', 'highscores', 'powergamers', 'lastkills', 'houses', 'guilds', 'wars', 'polls', 'bans', 'team', 'creatures', 'spells', 'commands', 'experienceStages', 'freeHouses', 'screenshots', 'movies', 'serverInfo', 'experienceTable', 'faq', 'points', 'gifts', 'bugtracker');
foreach($links as $link) {
    $template['link_' . $link] = internalLayoutLink($link);
}

$template['link_gifts_history'] = internalLayoutLink('gifts', 'show_history');
if($config['forum'] != '')
{
	if(strtolower($config['forum']) == 'site')
		$template['link_forum'] = "<a href='" . internalLayoutLink('forum') . "'>";
	else
		$template['link_forum'] = "<a href='" . $config['forum'] . "' target='_blank'>";
}

$twig->addGlobal('template_path', $template_path);
if($twig_loader && file_exists(BASE . $template_path . '/templates'))
	$twig_loader->prependPath(BASE . $template_path . '/templates');
?>
