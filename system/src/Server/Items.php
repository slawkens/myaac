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

namespace MyAAC\Server;

use MyAAC\Cache\PHP as CachePHP;

class Items
{
	public static array $items = [];

	private static string $error = '';

	public static function getError(): string {
		return self::$error;
	}

	public static function load(): bool {
		if (file_exists(config('data_path') . TOML\Items::FILE)) {
			$items = new TOML\Items();
		}
		elseif (file_exists(config('data_path') . XML\Items::FILE)) {
			$items = new XML\Items();
		}
		else {
			self::$error = 'Cannot load items.xml or items.toml file. Files not found.';
			return false;
		}

		if (!$items->load()) {
			self::$error = $items->getError();
			return false;
		}

		return true;
	}

	public static function init(): void {
		if(count(self::$items) > 0) {
			return;
		}

		$cache_php = new CachePHP(config('cache_prefix'), CACHE . 'persistent/');
		self::$items = (array)$cache_php->get('items');
	}

	public static function get(int $id) {
		self::init();
		return self::$items[$id] ?? [];
	}
}
