<?php

namespace MyAAC\Server;

use MyAAC\Cache\Cache;

class ExpStages
{
	private static array $stages = [];

	public static function get() {
		if (count(self::$stages) == 0) {
			self::$stages = Cache::remember('exp_stages', 10 * 60, function () {
				if (file_exists(config('server_path') . TOML\ExpStages::FILE)) {
					$expStages = new TOML\ExpStages();
				}
				elseif (file_exists(config('data_path') . XML\ExpStages::FILE)) {
					$expStages = new XML\ExpStages();
				}
				elseif (file_exists(config('data_path') . Lua\ExpStages::FILE)) {
					$expStages = new Lua\ExpStages();
				}
				else {
					return [];
				}

				$expStages->load();

				return $expStages->get();
			});
		}

		return self::$stages;
	}
}
