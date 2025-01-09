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
	// display list of monsters
	$preview = setting('core.monsters_images_preview');
	$monsters = Monster::where('hide', '!=', 1)->when(!empty($_REQUEST['boss']), function ($query) {
		$query->where('rewardboss', 1);
	})->get()->toArray();

	if ($preview) {
		foreach($monsters as $key => &$monster)
		{
			$monster['img_link'] = getMonsterImgPath($monster['name']);
		}
	}

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

	function sort_by_chance($a, $b)
	{
		if ($a['chance'] == $b['chance']) {
			return 0;
		}
		return ($a['chance'] > $b['chance']) ? -1 : 1;
	}

	$title = $monster['name'] . " - Monsters";

	$monster['img_link']= getMonsterImgPath($monster_name);

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
