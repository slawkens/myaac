<?php

namespace MyAAC\Server;

use MyAAC\Cache\Cache;

class Groups
{
	private static array $groups = [];

	public static function get() {
		if (count(self::$groups) == 0) {
			self::$groups = Cache::remember('groups', 10 * 60, function () {
				if (file_exists(config('server_path') . TOML\Groups::FILE)) {
					$groups = new TOML\Groups();
				}
				else {
					$groups = new XML\Groups();
				}

				$groups->load();

				return $groups->get();
			});
		}

		return self::$groups;
	}
}
