<?php

namespace MyAAC\Server;

use MyAAC\Cache\Cache;

class Outfits
{
	public static function get()
	{
		return Cache::remember('outfits', 10 * 60, function () {
			if (file_exists(config('server_path') . 'config/outfits.toml')) {
				$outfits = new TOML\Outfits();
			}
			else {
				$outfits = new XML\Outfits();
			}

			$outfits->load();

			return $outfits->getOutfits();
		});
	}
}
