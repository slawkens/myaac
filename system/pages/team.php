<?php
/**
 * Team
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Gamemasters List';

if($config['account_country'])
	require SYSTEM . 'countries.conf.php';

$groups = new OTS_Groups_List();
if(!$groups->count())
{
	echo 'Error while reading groups.xml';
	return;
}

$newStyle = ($config['team_style'] == 2);
if(!$newStyle)
{
	echo '<div style="text-align:center"><h2>Support in game</h2></div>
		<table border="0" cellspacing="1" cellpadding="4" width="100%">' . getGroupHeader();
}

$group_tmp = array();
$i = 0;
$groupList = $groups->getGroups();
foreach($groupList as $id => $group)
{
	if($id <= 1)
		continue;

	$group_members = $group->getPlayersList();
	if(!count($group_members))
		continue;

	$members_count = 0;
	$groupNames = array();
	foreach($group_members as $member)
	{
		if(!admin() && $member->isHidden())
			continue;

		$members_count++;
		$flag = '';
		if($config['account_country'])
			$flag = getFlagImage($member->getAccount()->getCountry());

		$tmp = '<tr bgcolor="' . getStyle($i++) . '">';
		if(!$newStyle)
			$tmp .= '<td>' . ucfirst($group->getName()) . '</td>';

		$tmp .= '<td>' . $flag . ' ' . getPlayerLink($member->getName()) . '</td>';
		if($config['team_display_status'])
			$tmp .= '<td>' . ($member->isOnline() > 0 ? '<span style="color: green"><b>Online</b></span>' : '<span style="color: red"><b>Offline</b></span>') . '</td>';

		if($config['multiworld'] || $config['team_display_world'])
			$tmp .= '<td><span class="white"><b>' . getWorldName($member->getWorldId()) . '</b></span></td>';

		$lastLogin = '';
		if($config['team_display_lastlogin'])
		{
			$lastLogin = 'Never.';
			if($member->getLastLogin() > 0)
				$lastLogin = date("j F Y, g:i a", $member->getLastLogin());
		}

		$tmp .= '<td>' . $lastLogin . '</td></tr>';
		if($newStyle)
		{
			if(isset($groupNames[$group->getName()]))
				$groupNames[$group->getName()] .= $tmp;
			else
				$groupNames[$group->getName()] = $tmp;
		}
		else
			echo $tmp;
	}

	if($newStyle && $members_count > 0)
	{
		$group_tmp[$id] = '<div style="text-align:center"><h2>' . ucfirst($group->getName()) . 's</h2></div>
		<table border="0" cellspacing="1" cellpadding="4" width="100%">' . getGroupHeader(false) . $groupNames[$group->getName()] . '</table>';
	}
}

if($newStyle)
{
	for($i = $id; $i >= 0; $i--)
	{
		if(isset($group_tmp[$i]))
			echo $group_tmp[$i];
	}
}
else
	echo '</table>';

function getGroupHeader($groupField = true)
{
	global $config;

	$ret = '<tr bgcolor="' . $config['vdarkborder'] . '">';
	if($groupField)
		$ret .= '<td width="20%"><span class="white"><b>Group</b></span></td>';

	$ret .= '<td width="40%"><span class="white"><b>Name</b></span></td>';

	if($config['team_display_status'])
		$ret .= '<td width="20%"><span class="white"><b>Status</b></span></td>';

	if($config['multiworld'] || $config['team_display_world'])
		$ret .= '<td><span class="white"><b>World</b></span></td>';

	if($config['team_display_lastlogin'])
		$ret .= '<td width="20%"><span class="white"><b>Last login</b></span></td>';

	$ret .= '</tr>';
	return $ret;
}
?>
