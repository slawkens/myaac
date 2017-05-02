<?php
/**
 * Template parsing engine.
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.0.1
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

if(!file_exists($template_path . '/config.php'))
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
else
	require($template_path . '/config.php');

$template = array();
$template['link_account_manage'] = internalLayoutLink('account' . ($config['friendly_urls'] ? '/manage' : 'management'));
$template['link_account_create'] = internalLayoutLink(($config['friendly_urls'] ? 'account/create' : 'createaccount'));
$template['link_account_lost'] = internalLayoutLink(($config['friendly_urls'] ? 'account/lost' : 'lostaccount'));
$template['link_account_logout'] = internalLayoutLink(($config['friendly_urls'] ? 'account' : 'accountmanagement'), 'logout');

$template['link_news'] = internalLayoutLink('news');
$template['link_news_archive'] = internalLayoutLink('news' . ($config['friendly_urls'] ? '' : '') . 'archive');
$template['link_changelog'] = internalLayoutLink('changelog');
$template['link_rules'] = internalLayoutLink('rules');
$template['link_downloads'] = internalLayoutLink('downloads');
$template['link_characters'] = internalLayoutLink('characters');
$template['link_online'] = internalLayoutLink('online');
$template['link_highscores'] = internalLayoutLink('highscores');
$template['link_powergamers'] = internalLayoutLink('powergamers');
$template['link_lastkills'] = internalLayoutLink('lastkills');
$template['link_houses'] = internalLayoutLink('houses');
$template['link_guilds'] = internalLayoutLink('guilds');
$template['link_wars'] = internalLayoutLink('wars');
$template['link_polls'] = internalLayoutLink('polls');
$template['link_bans'] = internalLayoutLink('bans');
$template['link_team'] = internalLayoutLink('team');
$template['link_creatures'] = internalLayoutLink('creatures');
$template['link_spells'] = internalLayoutLink('spells');
$template['link_commands'] = internalLayoutLink('commands');
$template['link_experienceStages'] = internalLayoutLink('experienceStages');
$template['link_freeHouses'] = internalLayoutLink('freeHouses');
$template['link_screenshots'] = internalLayoutLink('screenshots');
$template['link_movies'] = internalLayoutLink('movies');
$template['link_serverInfo'] = internalLayoutLink('serverInfo');
$template['link_experienceTable'] = internalLayoutLink('experienceTable');
$template['link_faq'] = internalLayoutLink('faq');
$template['link_points'] = internalLayoutLink('points');
$template['link_gifts'] = internalLayoutLink('gifts');
$template['link_gifts_history'] = internalLayoutLink('gifts', 'show_history');
if($config['forum'] != '')
{
	if(strtolower($config['forum']) == 'site')
		$template['link_forum'] = "<a href='" . internalLayoutLink('forum') . "'>";
	else
		$template['link_forum'] = "<a href='" . $config['forum'] . "' target='_blank'>";
}
?>
