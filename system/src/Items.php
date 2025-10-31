<?php
/**
 * Items class
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

namespace MyAAC;

use MyAAC\Cache\PHP as CachePHP;
use MyAAC\Models\Spell;

class Items
{
	private static $error = '';
	public static $items;

	public static function loadFromXML($show = false)
	{
		$file_path = config('data_path') . 'items/items.xml';
		if (!file_exists($file_path)) {
			self::$error = 'Cannot load file ' . $file_path;
			return false;
		}

		$xml = new \DOMDocument;
		$xml->load($file_path);

		$items = array();
		foreach ($xml->getElementsByTagName('item') as $item) {
			if ($item->getAttribute('fromid')) {
				for ($id = $item->getAttribute('fromid'); $id <= $item->getAttribute('toid'); $id++) {
					$tmp = self::parseNode($id, $item, $show);
					$items[$tmp['id']] = $tmp['content'];
				}
			} else {
				$tmp = self::parseNode($item->getAttribute('id'), $item, $show);
				$items[$tmp['id']] = $tmp['content'];
			}
		}

		$cache_php = new CachePHP(config('cache_prefix'), CACHE . 'persistent/');
		$cache_php->set('items', $items, 5 * 365 * 24 * 60 * 60);
		return true;
	}

	public static function parseNode($id, $node, $show = false) {
		$name = $node->getAttribute('name');
		$article = $node->getAttribute('article');
		$plural = $node->getAttribute('plural');

		$attributes = array();
		foreach($node->getElementsByTagName('attribute') as $attr) {
			$attributes[strtolower($attr->getAttribute('key'))] = $attr->getAttribute('value');
		}

		return array('id' => $id, 'content' => array('article' => $article, 'name' => $name, 'plural' => $plural, 'attributes' => $attributes));
	}

	public static function getError() {
		return self::$error;
	}

	public static function load() {
		if(self::$items) {
			return;
		}

		$cache_php = new CachePHP(config('cache_prefix'), CACHE . 'persistent/');
		self::$items = $cache_php->get('items');
	}

	public static function get($id) {
		self::load();
		return self::$items[$id] ?? [];
	}

	public static function getDescription($id, $count = 1): string
	{
		$item = self::get($id);

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

		if(isset($attr['type']) && strtolower($attr['type']) == 'rune') {
			$spell = Spell::where('item_id', $id)->first();
			if($spell) {
				if($spell->level > 0 && $spell->maglevel > 0) {
					$s .= '. ' . ($count > 1 ? 'They' : 'It') . ' can only be used by ';
				}

				$configVocations = config('vocations');
				if(!empty(trim($spell->vocations))) {
					$vocations = json_decode($spell->vocations);
					if(count($vocations) > 0) {
						foreach($vocations as $voc => $show) {
							$vocations[$configVocations[$voc]] = $show;
						}
					}
				}
				else {
					$s .= 'players';
				}

				$s .= ' with';

				if ($spell->level > 0) {
					$s .= ' level ' . $spell->level;
				}

				if ($spell->maglevel > 0) {
					if ($spell->level > 0) {
						$s .= ' and';
					}

					$s .= ' magic level ' . $spell->maglevel;
				}

				$s .= ' or higher';
			}
		}

		if (!empty($item['weaponType'])) {
			if ($item['weaponType'] == 'distance' && isset($item['ammoType'])) {
				$s .= ' (Range:' . $item['range'];
			}

			if (isset($item['attack']) && $item['attack'] != 0) {
				$s .= ', Atk ' . ($item['attack'] > 0 ? '+' . $item['attack'] : '-' . $item['attack']);
			}

			if (isset($item['hitChance']) && $item['hitChance'] != -1) {
				$s .= ', Hit% ' . ($item['hitChance'] > 0 ? '+' . $item['hitChance'] : '-' . $item['hitChance']);
			}
			elseif ($item['weaponType'] != 'ammo') {
				
			}
		}

		return $s;
	}
}
