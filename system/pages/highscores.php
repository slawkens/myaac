<?php
/**
 * Highscores
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Highscores';

$configHighscoresCountryBox = config('highscores_country_box');
if(config('account_country') && $configHighscoresCountryBox)
	require SYSTEM . 'countries.conf.php';

$list = isset($_GET['list']) ? $_GET['list'] : '';
$_page = isset($_GET['page']) ? $_GET['page'] : 0;
$vocation = isset($_GET['vocation']) ? $_GET['vocation'] : 'all';

$add_sql = '';

$configHighscoresVocationBox = config('highscores_vocation_box');
$configVocations = config('vocations');
$configVocationsAmount = config('vocations_amount');

if($configHighscoresVocationBox && $vocation !== 'all')
{
	foreach($configVocations as $id => $name) {
		if(strtolower($name) == $vocation) {
			$add_vocs = array($id);

			$i = $id + $configVocationsAmount;
			while(isset($configVocations[$i])) {
				$add_vocs[] = $i;
				$i += $configVocationsAmount;
			}

			$add_sql = 'AND `vocation` IN (' . implode(', ', $add_vocs) . ')';
			break;
		}
	}
}

define('SKILL_FRAGS', -1);
define('SKILL_BALANCE', -2);

$skill = POT::SKILL__LEVEL;
if(is_numeric($list))
{
	$list = (int) $list;
	if($list >= POT::SKILL_FIRST && $list <= POT::SKILL__LAST)
		$skill = $list;
}
else
{
	switch($list)
	{
		case 'fist':
			$skill = POT::SKILL_FIST;
			break;

		case 'club':
			$skill = POT::SKILL_CLUB;
			break;

		case 'sword':
			$skill = POT::SKILL_SWORD;
			break;

		case 'axe':
			$skill = POT::SKILL_AXE;
			break;

		case 'distance':
			$skill = POT::SKILL_DIST;
			break;

		case 'shield':
			$skill = POT::SKILL_SHIELD;
			break;

		case 'fishing':
			$skill = POT::SKILL_FISH;
			break;

		case 'level':
		case 'experience':
			$skill = POT::SKILL_LEVEL;
			break;

		case 'magic':
			$skill = POT::SKILL__MAGLEVEL;
			break;

		case 'frags':
			if(config('highscores_frags'))
				$skill = SKILL_FRAGS;
			break;

		case 'balance':
			if(config('highscores_balance'))
				$skill = SKILL_BALANCE;
			break;
	}
}

$promotion = '';
if($db->hasColumn('players', 'promotion'))
	$promotion = ',promotion';

$online = '';
if($db->hasColumn('players', 'online'))
	$online = ',online';

$deleted = 'deleted';
if($db->hasColumn('players', 'deletion'))
	$deleted = 'deletion';

$outfit_addons = false;
$outfit = '';

$configHighscoresOutfit = config('highscores_outfit');

if($configHighscoresOutfit) {
	$outfit = ', lookbody, lookfeet, lookhead, looklegs, looktype';
	if($db->hasColumn('players', 'lookaddons')) {
		$outfit .= ', lookaddons';
		$outfit_addons = true;
	}
}

$needReCache = true;
$cacheKey = 'highscores_' . $skill . '_' . $vocation . '_' . $_page;

$cache = Cache::getInstance();
if ($cache->enabled()) {
	$tmp = '';
	if ($cache->fetch($cacheKey, $tmp)) {
		$highscores = unserialize($tmp);
		$needReCache = false;
	}
}

$configHighscoresPerPage = config('highscores_per_page');
$offset = $_page * $configHighscoresPerPage;

if (!isset($highscores) || empty($highscores)) {
	$limit = $configHighscoresPerPage + 1;

	if ($skill >= POT::SKILL_FIRST && $skill <= POT::SKILL_LAST) { // skills
		if ($db->hasColumn('players', 'skill_fist')) {// tfs 1.0
			$skill_ids = array(
				POT::SKILL_FIST => 'skill_fist',
				POT::SKILL_CLUB => 'skill_club',
				POT::SKILL_SWORD => 'skill_sword',
				POT::SKILL_AXE => 'skill_axe',
				POT::SKILL_DIST => 'skill_dist',
				POT::SKILL_SHIELD => 'skill_shielding',
				POT::SKILL_FISH => 'skill_fishing',
			);

			$highscores = $db->query('SELECT accounts.country, players.id,players.name' . $online . ',level,vocation' . $promotion . $outfit . ', ' . $skill_ids[$skill] . ' as value FROM accounts,players WHERE players.id NOT IN (' . implode(', ', config('highscores_ids_hidden')) . ') AND players.' . $deleted . ' = 0 AND players.group_id < ' . config('highscores_groups_hidden') . ' ' . $add_sql . ' AND accounts.id = players.account_id ORDER BY ' . $skill_ids[$skill] . ' DESC LIMIT ' . $limit . ' OFFSET ' . $offset)->fetchAll();
		} else
			$highscores = $db->query('SELECT accounts.country, players.id,players.name' . $online . ',value,level,vocation' . $promotion . $outfit . ' FROM accounts,players,player_skills WHERE players.id NOT IN (' . implode(', ', config('highscores_ids_hidden')) . ') AND players.' . $deleted . ' = 0 AND players.group_id < ' . config('highscores_groups_hidden') . ' ' . $add_sql . ' AND players.id = player_skills.player_id AND player_skills.skillid = ' . $skill . ' AND accounts.id = players.account_id ORDER BY value DESC, count DESC LIMIT ' . $limit . ' OFFSET ' . $offset)->fetchAll();
	} else if ($skill == SKILL_FRAGS) // frags
	{
		if ($db->hasTable('player_killers')) {
			$highscores = $db->query('SELECT accounts.country, players.id, players.name' . $online . ',level, vocation' . $promotion . $outfit . ', COUNT(`player_killers`.`player_id`) as value' .
				' FROM `accounts`, `players`, `player_killers` ' .
				' WHERE players.id NOT IN (' . implode(', ', config('highscores_ids_hidden')) . ') AND players.' . $deleted . ' = 0 AND players.group_id < ' . config('highscores_groups_hidden') . ' ' . $add_sql . ' AND players.id = player_killers.player_id AND accounts.id = players.account_id' .
				' GROUP BY `player_id`' .
				' ORDER BY value DESC' .
				' LIMIT ' . $limit . ' OFFSET ' . $offset)->fetchAll();
		} else {
			$db->query("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

			$highscores = $db->query('SELECT `a`.country, `p`.id, `p`.name' . $online . ',`p`.level, vocation' . $promotion . $outfit . ', COUNT(`pd`.`killed_by`) as value
			FROM `players` p
			LEFT JOIN `accounts` a ON `a`.`id` = `p`.`account_id`
			LEFT JOIN `player_deaths` pd ON `pd`.`killed_by` = `p`.`name`
			WHERE `p`.id NOT IN (' . implode(', ', config('highscores_ids_hidden')) . ')
			AND `p`.' . $deleted . ' = 0
			AND `p`.group_id < ' . config('highscores_groups_hidden') . ' ' . $add_sql . '
			AND `pd`.`unjustified` = 1
			GROUP BY `killed_by`
			ORDER BY value DESC
			LIMIT ' . $limit . ' OFFSET ' . $offset)->fetchAll();
		}
	} else if ($skill == SKILL_BALANCE) // balance
	{
		$highscores = $db->query('SELECT accounts.country, players.id,players.name' . $online . ',level,balance as value,vocation' . $promotion . $outfit . ' FROM accounts,players WHERE players.id NOT IN (' . implode(', ', config('highscores_ids_hidden')) . ') AND players.' . $deleted . ' = 0 AND players.group_id < ' . config('highscores_groups_hidden') . ' ' . $add_sql . ' AND accounts.id = players.account_id ORDER BY value DESC LIMIT ' . $limit . ' OFFSET ' . $offset)->fetchAll();
	} else {
		if ($skill == POT::SKILL__MAGLEVEL) {
			$highscores = $db->query('SELECT accounts.country, players.id,players.name' . $online . ',maglevel,level,vocation' . $promotion . $outfit . ' FROM accounts, players WHERE players.id NOT IN (' . implode(', ', config('highscores_ids_hidden')) . ') AND players.' . $deleted . ' = 0 ' . $add_sql . ' AND players.group_id < ' . config('highscores_groups_hidden') . ' AND accounts.id = players.account_id ORDER BY maglevel DESC, manaspent DESC LIMIT ' . $limit . ' OFFSET ' . $offset)->fetchAll();
		} else { // level
			$highscores = $db->query('SELECT accounts.country, players.id,players.name' . $online . ',level,experience,vocation' . $promotion . $outfit . ' FROM accounts, players WHERE players.id NOT IN (' . implode(', ', config('highscores_ids_hidden')) . ') AND players.' . $deleted . ' = 0 ' . $add_sql . ' AND players.group_id < ' . config('highscores_groups_hidden') . ' AND accounts.id = players.account_id ORDER BY level DESC, experience DESC LIMIT ' . $limit . ' OFFSET ' . $offset)->fetchAll();
			$list = 'experience';
		}
	}
}

if ($cache->enabled() && $needReCache) {
	$cache->set($cacheKey, serialize($highscores), config('highscores_cache_ttl') * 60);
}

$online_exist = false;
if($db->hasColumn('players', 'online'))
	$online_exist = true;

$players = array();
foreach($highscores as $player) {
	$players[] = $player['id'];
}

if($db->hasTable('players_online') && count($players) > 0) {
	$query = $db->query('SELECT `player_id`, 1 FROM `players_online` WHERE `player_id` IN (' . implode(', ', $players) . ')')->fetchAll();
	foreach($query as $t) {
		$is_online[$t['player_id']] = true;
	}
}

$show_link_to_next_page = false;
$i = 0;

$configHighscoresVocation = config('highscores_vocation');

foreach($highscores as $id => &$player)
{
	if(isset($is_online)) {
		$player['online'] = (isset($is_online[$player['id']]) ? 1 : 0);
	} else {
		if(!isset($player['online'])) {
			$player['online'] = 0;
		}
	}

	if(++$i <= $configHighscoresPerPage)
	{
		if($skill == POT::SKILL__MAGIC)
			$player['value'] = $player['maglevel'];
		else if($skill == POT::SKILL__LEVEL) {
			$player['value'] = $player['level'];
			$player['experience'] = number_format($player['experience']);
		}

		if($configHighscoresVocation) {
			if(isset($player['promotion'])) {
				if((int)$player['promotion'] > 0) {
					$player['vocation'] += ($player['promotion'] * $configVocationsAmount);
				}
			}

			$tmp = 'Unknown';
			if(isset($configVocations[$player['vocation']])) {
				$tmp = $configVocations[$player['vocation']];
			}

			$player['vocation'] = $tmp;
		}


		$player['link'] = getPlayerLink($player['name'], false);
		$player['flag'] = getFlagImage($player['country']);
		$player['outfit'] = '<img style="position:absolute;margin-top:' . (in_array($player['looktype'], array(75, 266, 302)) ? '-15px;margin-left:5px' : '-45px;margin-left:-25px') . ';" src="' . config('outfit_images_url') . '?id=' . $player['looktype'] . ($outfit_addons ? '&addons=' . $player['lookaddons'] : '') . '&head=' . $player['lookhead'] . '&body=' . $player['lookbody'] . '&legs=' . $player['looklegs'] . '&feet=' . $player['lookfeet'] . '" alt="" />';
		$player['rank'] = $offset + $i;
	}
	else {
		unset($highscores[$id]);
		$show_link_to_next_page = true;
		break;
	}
}

if(!$i) {
	$extra = ($configHighscoresOutfit ? 1 : 0);
	echo '<tr bgcolor="' . config('darkborder') . '"><td colspan="' . ($skill == POT::SKILL__LEVEL ? 5 + $extra : 4 + $extra) . '">No records yet.</td></tr>';
}

//link to previous page if actual page is not first
$linkPreviousPage = '';
if($_page > 0) {
	$linkPreviousPage = getLink('highscores') . '/' . $list . ($vocation !== 'all' ? '/' . $vocation : '') . '/' . ($_page - 1);
}

//link to next page if any result will be on next page
$linkNextPage = '';
if($show_link_to_next_page) {
	$linkNextPage = getLink('highscores') . '/' . $list . ($vocation !== 'all' ? '/' . $vocation : '') . '/' . ($_page + 1);
}


$types = array(
	'experience' => 'Experience',
	'magic' => 'Magic',
	'shield' => 'Shielding',
	'distance' => 'Distance',
	'club' => 'Club',
	'sword' => 'Sword',
	'axe' => 'Axe',
	'fist' => 'Fist',
	'fishing' => 'Fishing',
);

if(config('highscores_frags')) {
	$types['frags'] = 'Frags';
}
if(config('highscores_balance'))
	$types['balance'] = 'Balance';

/** @var Twig\Environment $twig */
$twig->display('highscores.html.twig', [
	'highscores' => $highscores,
	'list' => $list,
	'skill' => $skill,
	'skillName' => ($skill == SKILL_FRAGS ? 'Frags' : ($skill == SKILL_BALANCE ? 'Balance' : getSkillName($skill))),
	'levelName' => ($skill != SKILL_FRAGS && $skill != SKILL_BALANCE ? 'Level' : ($skill == SKILL_BALANCE ? 'Balance' : 'Frags')),
	'vocation' => $vocation !== 'all' ? $vocation :  null,
	'types' => $types,
	'linkPreviousPage' => $linkPreviousPage,
	'linkNextPage' => $linkNextPage,
]);