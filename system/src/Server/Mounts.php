<?php

namespace MyAAC\Server;

use MyAAC\Cache\Cache;

class Mounts
{
	private static array $mounts = [];

	public static function get()
	{
		if (count(self::$mounts) == 0) {
			self::$mounts = Cache::remember('mounts', 10 * 60, function () {
				if (file_exists(config('server_path') . TOML\Mounts::FILE)) {
					$mounts = new TOML\Mounts();
				}
				else {
					$mounts = new XML\Mounts();
				}

				$mounts->load();

				return $mounts->get();
			});
		}

		return self::$mounts;
	}
}
