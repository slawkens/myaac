<?php

namespace MyAAC\Server;

use MyAAC\Cache\Cache;

class Groups
{
	private static array $groups = [];

	public function __construct() {
		self::$groups = Cache::remember('groups', 10 * 60, function () {
			if (file_exists(config('server_path') . 'config/groups.toml')) {
				$groups = new TOML\Groups();
			}
			else {
				$groups = new XML\Groups();
			}

			$groups->load();

			return $groups->getGroups();
		});
	}

	public static function getGroups(): array {
		return self::$groups;
	}
}
