<?php
/**
 * Weapons class
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Models\Weapon;

defined('MYAAC') or die('Direct access not allowed!');

class Weapons {
	private static $error = '';

	public static function loadFromXML($show = false)
	{
		global $config;

		try {
			Weapon::query()->delete();
		} catch (PDOException $error) {
		}

		$file_path = $config['data_path'] . 'weapons/weapons.xml';
		if (!file_exists($file_path)) {
			self::$error = 'Cannot load file ' . $file_path;
			return false;
		}

		$xml = new DOMDocument;
		$xml->load($file_path);

		foreach ($xml->getElementsByTagName('wand') as $weapon) {
			self::parseNode($weapon, $show);
		}
		foreach ($xml->getElementsByTagName('melee') as $weapon) {
			self::parseNode($weapon, $show);
		}
		foreach ($xml->getElementsByTagName('distance') as $weapon) {
			self::parseNode($weapon, $show);
		}

		return true;
	}

	public static function parseNode($node, $show = false) {
		global $config;

		$id = (int)$node->getAttribute('id');
		$vocations_ids = array_flip($config['vocations']);
		$level = (int)$node->getAttribute('level');
		$maglevel = (int)$node->getAttribute('maglevel');

		$vocations = array();
		foreach($node->getElementsByTagName('vocation') as $vocation) {
			$show = $vocation->getAttribute('showInDescription');
			if(!empty($vocation->getAttribute('id')))
				$voc_id = $vocation->getAttribute('id');
			else {
				$voc_id = $vocations_ids[$vocation->getAttribute('name')];
			}

			$vocations[$voc_id] = strlen($show) == 0 || $show != '0';
		}

		if(Weapon::find($id)) {
			if($show) {
				warning('Duplicated weapon with id: ' . $id);
			}
		}
		else {
			Weapon::create([
				'id' => $id, 'level' => $level, 'maglevel' => $maglevel, 'vocations' => json_encode($vocations)
			]);
		}
	}

	public static function getError() {
		return self::$error;
	}
}
