<?php
/**
 * Monsters
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @author    Lee
 * @copyright 2020 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Models\Monster;

defined('MYAAC') or die('Direct access not allowed!');
$title = 'Monsters';

if (empty($_REQUEST['name'])) {
	$preview = setting('core.monsters_images_preview');

	// display list of monsters
	$monsters = MyAAC\Cache::remember('monsters', 30 * 60, function () use ($preview) {
		$monsters = Monster::where('hide', '!=', 1)->when(!empty($_REQUEST['boss']), function ($query) {
			$query->where('rewardboss', 1);
		})->get()->toArray();

		if ($preview) {
			foreach($monsters as &$monster) {
				$monster['img_link'] = getMonsterImage($monster);
			}
		}

		return $monsters;
	});

	$twig->display('monsters.html.twig', array(
		'monsters' => $monsters,
		'preview' => $preview
	));

	return;
}

// display monster
$monster_name = urldecode(stripslashes(ucwords(strtolower($_REQUEST['name']))));
$monsterModel = Monster::where('hide', '!=', 1)->where('name', $monster_name)->first();

if ($monsterModel && isset($monsterModel->name)) {
	/** @var array $monster */
	$monster = $monsterModel->toArray();

	function sort_by_chance($a, $b): int
	{
		if ($a['chance'] == $b['chance']) {
			return 0;
		}
		return ($a['chance'] > $b['chance']) ? -1 : 1;
	}

	$title = $monster['name'] . " - Monsters";

	$monster['img_link']= getMonsterImage($monster);

	$voices = json_decode($monster['voices'], true);
	$summons = json_decode($monster['summons'], true);
	$elements = json_decode($monster['elements'], true);
	$immunities = json_decode($monster['immunities'], true);
	$loot = json_decode($monster['loot'], true);
	if (!empty($loot)) {
		usort($loot, 'sort_by_chance');
	}

	foreach ($loot as &$item) {
		$item['name'] = getItemNameById($item['id']);
		$item['rarity_chance'] = round($item['chance'] / 1000, 2);
		$item['rarity'] = getItemRarity($item['chance']);
		$item['tooltip'] = ucfirst($item['name']) . '<br/>Chance: ' . $item['rarity'] . (setting('core.monsters_loot_percentage') ? ' ('. $item['rarity_chance'] .'%)' : '') . '<br/>Max count: ' . $item['count'];
	}

	$monster['loot'] = $loot ?? null;
	$monster['voices'] = $voices ?? null;
	$monster['summons'] = $summons ?? null;
	$monster['elements'] = $elements ?? null;
	$monster['immunities'] = $immunities ?? null;

	$twig->display('monster.html.twig', array(
		'monster' => $monster,
	));

} else {
	echo "Monster with name <b>" . htmlspecialchars($monster_name) . "</b> doesn't exist.";
}

// back button
$twig->display('monsters.back_button.html.twig');

function getMonsterImage($monster): string
{
	$outfit = json_decode($monster['look'], true);

	if (!empty($outfit['typeEx'])) {
		return setting('core.item_images_url') . $outfit['typeEx'] . setting('core.item_images_extension');
	}

	if (isset($outfit['type'])) {
		$getValue = function ($val) use ($outfit) {
			return (!empty($outfit[$val])
				? '&' . $val . '=' . $outfit[$val] : '');
		};

		return setting('core.outfit_images_url') . '?id=' . $outfit['type'] . $getValue('addons') . $getValue('head') . $getValue('body') . $getValue('legs') . $getValue('feet');
	}

	return getMonsterImgPath($monster['name']);
}

function getMonsterImgPath($name): string
{
	$monster_path = setting('core.monsters_images_url');
	$monster_gfx_name = trim(strtolower($name)) . setting('core.monsters_images_extension');
	if (!file_exists($monster_path . $monster_gfx_name)) {
		$monster_gfx_name = str_replace(" ", "", $monster_gfx_name);
		if (file_exists($monster_path . $monster_gfx_name)) {
			return $monster_path . $monster_gfx_name;
		} else {
			return $monster_path . 'nophoto.png';
		}
	} else {
		return $monster_path . $monster_gfx_name;
	}
}
