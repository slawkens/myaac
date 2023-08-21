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

use MyAAC\Models\Player;
use MyAAC\Models\PlayerDeath;
use MyAAC\Models\PlayerKillers;

defined('MYAAC') or die('Direct access not allowed!');
$title = 'Highscores';

$settingHighscoresCountryBox = setting('core.highscores_country_box');
if(config('account_country') && $settingHighscoresCountryBox)
	require SYSTEM . 'countries.conf.php';

$list = $_GET['list'] ?? 'experience';
$page = $_GET['page'] ?? 1;
$vocation = $_GET['vocation'] ?? 'all';

if(!is_numeric($page) || $page < 1 || $page > PHP_INT_MAX) {
	$page = 1;
}

$query = Player::query();

$settingHighscoresVocationBox = setting('core.highscores_vocation_box');
$configVocations = config('vocations');
$configVocationsAmount = config('vocations_amount');

if($settingHighscoresVocationBox && $vocation !== 'all')
{
	foreach($configVocations as $id => $name) {
		if(strtolower($name) == $vocation) {
			$add_vocs = array($id);

			$i = $id + $configVocationsAmount;
			while(isset($configVocations[$i])) {
				$add_vocs[] = $i;
				$i += $configVocationsAmount;
			}

			$query->whereIn('players.vocation', $add_vocs);
			break;
		}
	}
}

const SKILL_FRAGS = -1;
const SKILL_BALANCE = -2;

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
			if(setting('core.highscores_frags'))
				$skill = SKILL_FRAGS;
			break;

		case 'balance':
			if(setting('core.highscores_balance'))
				$skill = SKILL_BALANCE;
			break;
	}
}

$promotion = '';
if($db->hasColumn('players', 'promotion'))
	$promotion = ',players.promotion';

$outfit_addons = false;
$outfit = '';

$settingHighscoresOutfit = setting('core.highscores_outfit');

if($settingHighscoresOutfit) {
	$outfit = ', lookbody, lookfeet, lookhead, looklegs, looktype';
	if($db->hasColumn('players', 'lookaddons')) {
		$outfit .= ', lookaddons';
		$outfit_addons = true;
	}
}

$configHighscoresPerPage = setting('core.highscores_per_page');
$limit = $configHighscoresPerPage + 1;

$needReCache = true;
$cacheKey = 'highscores_' . $skill . '_' . $vocation . '_' . $page . '_' . $configHighscoresPerPage;

$cache = Cache::getInstance();
if ($cache->enabled()) {
	$tmp = '';
	if ($cache->fetch($cacheKey, $tmp)) {
		$highscores = unserialize($tmp);
		$needReCache = false;
	}
}

$offset = ($page - 1) * $configHighscoresPerPage;
$query->join('accounts', 'accounts.id', '=', 'players.account_id')
	->withOnlineStatus()
	->whereNotIn('players.id', setting('core.highscores_ids_hidden'))
	->notDeleted()
	->where('players.group_id', '<', setting('core.highscores_groups_hidden'))
	->limit($limit)
	->offset($offset)
	->selectRaw('accounts.country, players.id, players.name, players.account_id, players.level, players.vocation' . $outfit . $promotion)
	->orderByDesc('value');

if (!isset($highscores) || empty($highscores)) {
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

			$query->addSelect($skill_ids[$skill] . ' as value');
		} else {
			$query
				->join('player_skills', 'player_skills.player_id', '=', 'players.id')
				->where('skillid', $skill)
				->addSelect('player_skills.skillid as value');
		}
	} else if ($skill == SKILL_FRAGS) // frags
	{
		if ($db->hasTable('player_killers')) {
			$query->addSelect(['value' => PlayerKillers::where('player_killers.player_id', 'players.id')->selectRaw('COUNT(*)')]);
		} else {
			$query->addSelect(['value' => PlayerDeath::unjustified()->where('player_deaths.killed_by', 'players.name')->selectRaw('COUNT(*)')]);
		}
	} else if ($skill == SKILL_BALANCE) // balance
	{
		$query
			->addSelect('players.balance as value');
	} else {
		if ($skill == POT::SKILL__MAGLEVEL) {
			$query
				->addSelect('players.maglevel as value', 'players.maglevel')
				->orderBy('manaspent');
		} else { // level
			$query
				->addSelect('players.level as value', 'players.experience')
				->orderBy('experience');
			$list = 'experience';
		}
	}
}

$highscores = $query->get()->map(function($row) {
	$tmp = $row->toArray();
	$tmp['online'] = $row->online_status;
	$tmp['vocation'] = $row->vocation_name;
	unset($tmp['online_table']);

	return $tmp;
})->toArray();

if ($cache->enabled() && $needReCache) {
	$cache->set($cacheKey, serialize($highscores), setting('core.highscores_cache_ttl') * 60);
}

$show_link_to_next_page = false;
$i = 0;

$settingHighscoresVocation = setting('core.highscores_vocation');

foreach($highscores as $id => &$player)
{
	if(++$i <= $configHighscoresPerPage)
	{
		if($skill == POT::SKILL__MAGIC)
			$player['value'] = $player['maglevel'];
		else if($skill == POT::SKILL__LEVEL) {
			$player['value'] = $player['level'];
			$player['experience'] = number_format($player['experience']);
		}

		if(!$settingHighscoresVocation) {
			unset($player['vocation']);
		}

		$player['link'] = getPlayerLink($player['name'], false);
		$player['flag'] = getFlagImage($player['country']);
		if($settingHighscoresOutfit) {
			$player['outfit'] = '<img style="position:absolute;margin-top:' . (in_array($player['looktype'], config('outfit_images_wrong_looktypes')) ? '-15px;margin-left:5px' : '-45px;margin-left:-25px') . ';" src="' . config('outfit_images_url') . '?id=' . $player['looktype'] . ($outfit_addons ? '&addons=' . $player['lookaddons'] : '') . '&head=' . $player['lookhead'] . '&body=' . $player['lookbody'] . '&legs=' . $player['looklegs'] . '&feet=' . $player['lookfeet'] . '" alt="" />';
		}
		$player['rank'] = $offset + $i;
	}
	else {
		unset($highscores[$id]);
		$show_link_to_next_page = true;
		break;
	}
}

//link to previous page if actual page is not first
$linkPreviousPage = '';
if($page > 1) {
	$linkPreviousPage = getLink('highscores') . '/' . $list . ($vocation !== 'all' ? '/' . $vocation : '') . '/' . ($page - 1);
}

//link to next page if any result will be on next page
$linkNextPage = '';
if($show_link_to_next_page) {
	$linkNextPage = getLink('highscores') . '/' . $list . ($vocation !== 'all' ? '/' . $vocation : '') . '/' . ($page + 1);
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

if(setting('core.highscores_frags')) {
	$types['frags'] = 'Frags';
}
if(setting('core.highscores_balance'))
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
