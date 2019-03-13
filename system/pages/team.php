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

$outfit_addons = false;
$outfit = '';
if($config['team_display_outfit']) {
	$outfit = ', lookbody, lookfeet, lookhead, looklegs, looktype';
	if($db->hasColumn('players', 'lookaddons')) {
		$outfit .= ', lookaddons';
		$outfit_addons = true;
	}
}

$groupMember = array();
$groupList = $groups->getGroups();
foreach($groupList as $id => $group)
{
	if($id <= 1)
		continue;

	$group_members = $group->getPlayersList();
	if(!count($group_members))
		continue;

	$members = array();
	foreach($group_members as $member)
	{
		if(!admin() && $member->isHidden())
			continue;

		$lastLogin = 'Never.';
		if($member->getLastLogin() > 0)
			$lastLogin = date("j F Y, g:i a", $member->getLastLogin());

		$members[] = array(
			'group_name' => $group->getName(),
			'player' => $member,
			'outfit' => $config['team_display_outfit'] ? $config['outfit_images_url'] . '?id=' . $member->getLookType() . ($outfit_addons ? '&addons=' . $member->getLookAddons() : '') . '&head=' . $member->getLookHead() . '&body=' . $member->getLookBody() . '&legs=' . $member->getLookLegs() . '&feet=' . $member->getLookFeet() : null,
			'status' => $member->isOnline(),
			'link' => getPlayerLink($member->getName()),
			'flag_image' => getFlagImage($member->getAccount()->getCountry()),
			'world_name' => getWorldName($member->getWorldId()),
			'last_login' => $lastLogin
		);	
	}

	$groupMember[] = array(
		'group_name' => $group->getName(),
		'members' => $members
	);	
}

$twig->display('team.html.twig', array(
	'groupmember' => $groupMember
));
?>