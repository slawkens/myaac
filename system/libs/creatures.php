<?php
/**
 * Creatures class
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Models\Monster;

defined('MYAAC') or die('Direct access not allowed!');

require_once LIBS . 'items.php';
class Creatures {
	/**
	 * @var OTS_MonstersList
	 */
	private static $monstersList;
	private static $lastError = '';

	public static function loadFromXML($show = false) {
		try {
			Monster::query()->delete();
		} catch(Exception $error) {}

		if($show) {
			echo '<h2>Reload monsters.</h2>';
			echo "<h2>All records deleted from table '" . TABLE_PREFIX . "monsters' in database.</h2>";
		}

		try {
			self::$monstersList = new OTS_MonstersList(config('data_path') . 'monster/');
		}
		catch(Exception $e) {
			self::$lastError = $e->getMessage();
			return false;
		}

		$items = array();
		Items::load();
		foreach((array)Items::$items as $id => $item) {
			$items[$item['name']] = $id;
		}

		//$names_added must be an array
		$names_added[] = '';
		//add monsters
		foreach(self::$monstersList as $lol) {
			$monster = self::$monstersList->current();
			if(!$monster->loaded()) {
				if($show) {
					warning('Error while adding monster: ' . self::$monstersList->currentFile());
				}
				continue;
			}

			//load monster mana needed to summon/convince
			$mana = $monster->getManaCost();

			//load monster name
			$name = $monster->getName();
			//load monster health
			$health = $monster->getHealth();
			//load monster speed and calculate "speed level"
			$speed_ini = $monster->getSpeed();
			if($speed_ini <= 220) {
				$speed_lvl = 1;
			} else {
				$speed_lvl = ($speed_ini - 220) / 2;
			}
			//check "is monster use haste spell"
			$defenses = $monster->getDefenses();
			$use_haste = 0;
			foreach($defenses as $defense) {
				if($defense == 'speed') {
					$use_haste = 1;
				}
			}

			//load race
			$race = $monster->getRace();
			$armor = $monster->getArmor();
			$defensev = $monster->getDefense();

			//load look
			$look = $monster->getLook();

			//load monster flags
			$flags = $monster->getFlags();
			if(!isset($flags['summonable']))
				$flags['summonable'] = '0';
			if(!isset($flags['convinceable']))
				$flags['convinceable'] = '0';

			if(!isset($flags['pushable']))
				$flags['pushable'] = '0';
			if(!isset($flags['canpushitems']))
				$flags['canpushitems'] = '0';
			if(!isset($flags['canpushcreatures']))
				$flags['canpushcreatures'] = '0';
			if(!isset($flags['runonhealth']))
				$flags['runonhealth'] = '0';
			if(!isset($flags['canwalkonenergy']))
				$flags['canwalkonenergy'] = '0';
			if(!isset($flags['canwalkonpoison']))
				$flags['canwalkonpoison'] = '0';
			if(!isset($flags['canwalkonfire']))
				$flags['canwalkonfire'] = '0';
			if(!isset($flags['hostile']))
				$flags['hostile'] = '0';
			if(!isset($flags['attackable']))
				$flags['attackable'] = '0';
			if(!isset($flags['rewardboss']))
				$flags['rewardboss'] = '0';

			$summons = $monster->getSummons();
			$loot = $monster->getLoot();
			foreach($loot as &$item) {
				if(!Validator::number($item['id'])) {
					if(isset($items[$item['id']])) {
						$item['id'] = $items[$item['id']];
					}
				}
			}
			if(!in_array($name, $names_added)) {
				try {
					Monster::create(array(
						'name' => $name,
						'mana' => empty($mana) ? 0 : $mana,
						'exp' => $monster->getExperience(),
						'health' => $health,
						'speed_lvl' => $speed_lvl,
						'use_haste' => $use_haste,
						'voices' => json_encode($monster->getVoices()),
						'immunities' => json_encode($monster->getImmunities()),
						'elements' => json_encode($monster->getElements()),
						'summonable' => $flags['summonable'] > 0 ? 1 : 0,
						'convinceable' => $flags['convinceable'] > 0 ? 1 : 0,
						'pushable' => $flags['pushable'] > 0 ? 1 : 0,
						'canpushitems' => $flags['canpushitems'] > 0 ? 1 : 0,
						'canpushcreatures' => $flags['canpushcreatures'] > 0 ? 1 : 0,
						'runonhealth' => $flags['runonhealth'] > 0 ? 1 : 0,
						'canwalkonenergy' => $flags['canwalkonenergy'] > 0 ? 1 : 0,
						'canwalkonpoison' => $flags['canwalkonpoison'] > 0 ? 1 : 0,
						'canwalkonfire' => $flags['canwalkonfire'] > 0 ? 1 : 0,
						'hostile' => $flags['hostile'] > 0 ? 1 : 0,
						'attackable' => $flags['attackable'] > 0 ? 1 : 0,
						'rewardboss' => $flags['rewardboss'] > 0 ? 1 : 0,
						'defense' => $defensev,
						'armor' => $armor,
						'race' => $race,
						'loot' => json_encode($loot),
						'look' => json_encode($look),
						'summons' => json_encode($summons)
					));

					if($show) {
						success('Added: ' . $name . '<br/>');
					}
				}
				catch(Exception $error) {
					if($show) {
						warning('Error while adding monster (' . $name . '): ' . $error->getMessage());
					}
				}

				$names_added[] = $name;
			}
		}

		return true;
	}

	public static function getMonstersList() {
		return self::$monstersList;
	}

	public static function getLastError() {
		return self::$lastError;
	}
}
