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
defined('MYAAC') or die('Direct access not allowed!');
$title = "Creatures";

if (empty($_REQUEST['creature'])) {
	$creatures = $db->query('SELECT * FROM `' . TABLE_PREFIX . 'monsters` WHERE `hidden` != 1 '.(empty($_REQUEST['boss']) ? '': 'AND `rewardboss` = 1').' ORDER BY name asc');
	$twig->display('creatures.html.twig', array(
		'creatures' => $creatures->fetchAll(),
		'preview' => config('creature_images_preview'),
	));

} else {
	$creature_name = urldecode(stripslashes(ucwords(strtolower($_REQUEST['creature']))));
	$prep = $db->prepare('SELECT * FROM `' . TABLE_PREFIX . 'monsters` WHERE `hidden` != 1 AND `name` = ? LIMIT 1;');
	$prep->execute([$creature_name]);
	$creature = $prep->fetch();

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

		$loot_list = [];
		foreach ($loot as $item) {
			$item['name'] = getItemNameById($item['id']);
			$item['rarity_chance'] = round($item['chance'] / 1000, 2);
			$item['rarity'] = getItemRarity($item['chance']);
			$item['tooltip'] = ucfirst($item['name']) . '<br/>Chance: ' . $item['rarity'] . (config('loot_show_percentage') ? ' ('. $item['rarity_chance'] .'%)' : '') . '<br/>Max count: ' . $item['count'];
			$loot_list[] =  $item;
		}

		$creature['loot'] = isset($loot_list) ? $loot_list : null;
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

//back button
	$twig->display('creatures.back_button.html.twig');
}
?>

