<?php
/**
 * Creatures
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
$title = 'Creatures';

if (empty($_REQUEST['name'])) {
	// display list of monsters
	$preview = config('monsters_images_preview');
	$creatures = Monster::where('hidden', '!=', 1)->when(!empty($_REQUEST['boss']), function ($query) {
		$query->where('rewardboss', 1);
	})->get()->toArray();

	if ($preview) {
		foreach($creatures as $key => &$creature)
		{
			$creature['img_link'] = getCreatureImgPath($creature['name']);
		}
	}

	$twig->display('creatures.html.twig', array(
		'creatures' => $creatures,
		'preview' => $preview
	));

	return;
}

// display monster
$creature_name = urldecode(stripslashes(ucwords(strtolower($_REQUEST['name']))));
$creature = Monster::where('hidden', '!=', 1)->where('name', $creature_name)->first()->toArray();

if (isset($creature['name'])) {
	function sort_by_chance($a, $b)
	{
		if ($a['chance'] == $b['chance']) {
			return 0;
		}
		return ($a['chance'] > $b['chance']) ? -1 : 1;
	}

	$title = $creature['name'] . " - Creatures";

	$creature['img_link']= getCreatureImgPath($creature_name);

	$voices = json_decode($creature['voices'], true);
	$summons = json_decode($creature['summons'], true);
	$elements = json_decode($creature['elements'], true);
	$immunities = json_decode($creature['immunities'], true);
	$loot = json_decode($creature['loot'], true);
	usort($loot, 'sort_by_chance');

	foreach ($loot as &$item) {
		$item['name'] = getItemNameById($item['id']);
		$item['rarity_chance'] = round($item['chance'] / 1000, 2);
		$item['rarity'] = getItemRarity($item['chance']);
		$item['tooltip'] = ucfirst($item['name']) . '<br/>Chance: ' . $item['rarity'] . (config('monsters_loot_percentage') ? ' ('. $item['rarity_chance'] .'%)' : '') . '<br/>Max count: ' . $item['count'];
	}

	$creature['loot'] = isset($loot) ? $loot : null;
	$creature['voices'] = isset($voices) ? $voices : null;
	$creature['summons'] = isset($summons) ? $summons : null;
	$creature['elements'] = isset($elements) ? $elements : null;
	$creature['immunities'] = isset($immunities) ? $immunities : null;

	$twig->display('creature.html.twig', array(
		'creature' => $creature,
	));

} else {
	echo "Creature with name <b>" . $creature_name . "</b> doesn't exist.";
}

// back button
$twig->display('creatures.back_button.html.twig');
