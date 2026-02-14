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
	public static $items;

	public static function load(): bool {
		$file_path = config('data_path') . 'items/items.toml';
		if (file_exists($file_path)) {
			$items = new Server\TOML\Items();
		}
		elseif (file_exists(config('data_path') . 'items/items.xml')) {
			$items = new Server\XML\Items();
		}
		else {
			return false;
		}

		$items->load();

		return true;
	}

	public static function init(): void {
		if(count(self::$items) > 0) {
			return;
		}

		$cache_php = new CachePHP(config('cache_prefix'), CACHE . 'persistent/');
		self::$items = $cache_php->get('items');
	}

	public static function get($id) {
		self::init();
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
