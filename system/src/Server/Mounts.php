<?php

namespace MyAAC\Server;

use MyAAC\Cache\Cache;

class Mounts
{
	public static function get()
	{
		return Cache::remember('mounts', 10 * 60, function () {
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
}
