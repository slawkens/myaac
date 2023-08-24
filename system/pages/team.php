<?php
/**
 * Team
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Support in game';

if(setting('core.account_country'))
	require SYSTEM . 'countries.conf.php';

$groups = new OTS_Groups_List();
if(!$groups->count())
{
	echo 'Error while reading groups.xml';
	return;
}

$outfit_addons = false;
$outfit = '';
if(setting('core.team_outfit')) {
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
		/** @var OTS_Player $member */
		if(!admin() && $member->isHidden())
			continue;

		$lastLogin = 'Never.';
		if($member->getLastLogin() > 0)
			$lastLogin = date("j F Y, g:i a", $member->getLastLogin());

		$members[] = array(
			'group_name' => $group->getName(),
			'player' => $member,
			'outfit' => setting('core.team_outfit') ? setting('core.outfit_images_url') . '?id=' . $member->getLookType() . ($outfit_addons ? '&addons=' . $member->getLookAddons() : '') . '&head=' . $member->getLookHead() . '&body=' . $member->getLookBody() . '&legs=' . $member->getLookLegs() . '&feet=' . $member->getLookFeet() : null,
			'status' => setting('core.team_status') ? $member->isOnline() : null,
			'link' => getPlayerLink($member->getName()),
			'flag_image' => setting('core.account_country') ? getFlagImage($member->getAccount()->getCountry()) : null,
			'world_name' => (setting('core.multiworld') || setting('core.team_world')) ? getWorldName($member->getWorldId()) : null,
			'last_login' => setting('core.team_lastlogin') ? $lastLogin : null
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
