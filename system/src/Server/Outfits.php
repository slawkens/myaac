<?php

namespace MyAAC\Server;

use MyAAC\Cache\Cache;

class Outfits
{
	private static array $outfits = [];

	public static function get()
	{
		if (count(self::$outfits) == 0) {
			self::$outfits = Cache::remember('outfits', 10 * 60, function () {
				if (file_exists(config('server_path') . TOML\Outfits::FILE)) {
					$outfits = new TOML\Outfits();
				} else {
					$outfits = new XML\Outfits();
				}

				$outfits->load();

				return $outfits->get();
			});
		}

		return self::$outfits;
	}
}
