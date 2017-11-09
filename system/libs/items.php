<?php
/**
 * Items class
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

class Items
{
	private static $error = '';

	public static function loadFromXML($show = false)
	{
		global $config, $db;
		
		try {
			$db->query("DELETE FROM `myaac_items`;");
		} catch (PDOException $error) {
		}
		
		$file_path = $config['data_path'] . 'items/items.xml';
		if (!file_exists($file_path)) {
			self::$error = 'Cannot load file ' . $file_path;
			return false;
		}
		
		$xml = new DOMDocument;
		$xml->load($file_path);
		
		foreach ($xml->getElementsByTagName('item') as $item) {
			if ($item->getAttribute('fromid')) {
				for ($id = $item->getAttribute('fromid'); $id <= $item->getAttribute('toid'); $id++) {
					self::parseNode($id, $item, $show);
				}
			} else
				self::parseNode($item->getAttribute('id'), $item, $show);
			
		}
		
		return true;
	}
	
	public static function parseNode($id, $node, $show = false) {
		global $db;
		
		$name = $node->getAttribute('name');
		$article = $node->getAttribute('article');
		$plural = $node->getAttribute('plural');
		
		$attributes = array();
		foreach($node->getElementsByTagName('attribute') as $attr) {
			$attributes[strtolower($attr->getAttribute('key'))] = $attr->getAttribute('value');
		}
		
		$exist = $db->query('SELECT `id` FROM `' . TABLE_PREFIX . 'items` WHERE `id` = ' . $id);
		if($exist->rowCount() > 0) {
			if($show) {
				warning('Duplicated item with id: ' . $id);
			}
		}
		else {
			$db->insert(TABLE_PREFIX . 'items', array('id' => $id, 'article' => $article, 'name' => $name, 'plural' => $plural, 'attributes' => json_encode($attributes)));
		}
	}
	
	public static function getError() {
		return self::$error;
	}
	
	public static function getItem($id) {
		global $db;
		
		$item = $db->select(TABLE_PREFIX . 'items', array('id' => $id));
		$item['attributes'] = json_decode($item['attributes']);
		
		return $item;
	}
	
	public static function getDescription($id, $count = 1) {
		global $config, $db;
		
		$item = self::getItem($id);
		
		$attr = $item['attributes'];
		$s = '';
		if(!empty($item['name'])) {
			if($count > 1) {
				if($attr['showcount']) {
					$s .= $count . ' ';
				}
				
				if(!empty($item['plural'])) {
					$s .= $item['plural'];
				}
				else if((int)$attr['showcount'] == 0) {
					$s .= $item['name'];
				}
				else {
					$s .= $item['name'] . 's';
				}
			}
			else {
				if(!empty($item['aticle'])) {
					$s .= $item['article'] . ' ';
				}
				
				$s .= $item['name'];
			}
		}
		else
			$s .= 'an item of type ' . $item['id'];
		
		if(strtolower($attr['type']) == 'rune') {
			$query = $db->query('SELECT `level`, `maglevel`, `vocations` FROM `' . TABLE_PREFIX . 'spells` WHERE `item_id` = ' . $id);
			if($query->rowCount() == 1) {
				$query = $query->fetch();
				
				if($query['level'] > 0 && $query['maglevel'] > 0) {
					$s .= '. ' . ($count > 1 ? "They" : "It") . ' can only be used by ';
				}
				
				if(!empty(trim($query['vocations']))) {
					$vocations = json_decode($query['vocations']);
					if(count($vocations) > 0) {
						foreach($vocations as $voc => $show) {
							$vocations[$config['vocations'][$voc]] = $show;
						}
					}
				}
				else {
					$s .= 'players';
				}
			
				$s .= ' with';
				
			}
		}
		return $s;
	}
}