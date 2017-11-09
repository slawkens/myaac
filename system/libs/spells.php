<?php
/**
 * Spells class
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

class Spells {
	private static $spellsList = null;
	private static $lastError = '';
	
	public static function loadFromXML($show = false) {
		global $config, $db;
		
		try { $db->query('DELETE FROM `' . TABLE_PREFIX . 'spells`;'); } catch(PDOException $error) {}
		
		if($show) {
			echo '<h2>Reload spells.</h2>';
			echo '<h2>All records deleted from table <b>' . TABLE_PREFIX . 'spells</b> in database.</h2>';
		}
		
		try {
			self::$spellsList = new OTS_SpellsList($config['data_path'].'spells/spells.xml');
		}
		catch(Exception $e) {
			self::$lastError = $e->getMessage();
			return false;
		}
		
		//add conjure spells
		$conjurelist = self::$spellsList->getConjuresList();
		if($show) {
			echo "<h3>Conjure:</h3>";
		}
		
		foreach($conjurelist as $spellname) {
			$spell = self::$spellsList->getConjure($spellname);
			$name = $spell->getName();
			
			$words = $spell->getWords();
			if(strpos($words, '#') !== false)
				continue;
			
			try {
				$db->insert(TABLE_PREFIX . 'spells', array(
					'name' => $name,
					'words' => $words,
					'type' => 2,
					'mana' => $spell->getMana(),
					'level' => $spell->getLevel(),
					'maglevel' => $spell->getMagicLevel(),
					'soul' => $spell->getSoul(),
					'premium' => $spell->isPremium() ? 1 : 0,
					'vocations' => json_encode($spell->getVocations()),
					'conjure_count' => $spell->getConjureCount(),
					'hidden' => $spell->isEnabled() ? 0 : 1
				));

				if($show) {
					success('Added: ' . $name . '<br/>');
				}
			}
			catch(PDOException $error) {
				if($show) {
					warning('Error while adding spell (' . $name . '): ' . $error->getMessage());
				}
			}
		}
		
		// add instant spells
		$instantlist = self::$spellsList->getInstantsList();
		if($show) {
			echo "<h3>Instant:</h3>";
		}
		
		foreach($instantlist as $spellname) {
			$spell = self::$spellsList->getInstant($spellname);
			$name = $spell->getName();
			
			$words = $spell->getWords();
			if(strpos($words, '#') !== false)
				continue;
			
			try {
				$db->insert(TABLE_PREFIX . 'spells', array(
					'name' => $name,
					'words' => $words,
					'type' => 1,
					'mana' => $spell->getMana(),
					'level' => $spell->getLevel(),
					'maglevel' => $spell->getMagicLevel(),
					'soul' => $spell->getSoul(),
					'premium' => $spell->isPremium() ? 1 : 0,
					'vocations' => json_encode($spell->getVocations()),
					'conjure_count' => 0,
					'hidden' => $spell->isEnabled() ? 0 : 1
				));
				
				if($show) {
					success('Added: ' . $name . '<br/>');
				}
			}
			catch(PDOException $error) {
				if($show) {
					warning('Error while adding spell (' . $name . '): ' . $error->getMessage());
				}
			}
		}
		
		// add runes
		$runeslist = self::$spellsList->getRunesList();
		if($show) {
			echo "<h3>Runes:</h3>";
		}
		
		foreach($runeslist as $spellname) {
			$spell = self::$spellsList->getRune($spellname);

			$name = $spell->getName() . ' (rune)';

			try {
				$db->insert(TABLE_PREFIX . 'spells', array(
					'name' => $name,
					'words' => $spell->getWords(),
					'type' => 3,
					'mana' => $spell->getMana(),
					'level' => $spell->getLevel(),
					'maglevel' => $spell->getMagicLevel(),
					'soul' => $spell->getSoul(),
					'premium' => $spell->isPremium() ? 1 : 0,
					'vocations' => json_encode($spell->getVocations()),
					'conjure_count' => 0,
					'item_id' => $spell->getID(),
					'hidden' => $spell->isEnabled() ? 0 : 1
				));
				
				if($show) {
					success('Added: ' . $name . '<br/>');
				}
			}
			catch(PDOException $error) {
				if($show) {
					warning('Error while adding spell (' . $name . '): ' . $error->getMessage());
				}
			}
		}
		
		return true;
	}
	
	public static function getSpellsList() {
		return self::$spellsList;
	}
	
	public static function getLastError() {
		return self::$lastError;
	}
}