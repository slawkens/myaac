<?php

namespace MyAAC\Server;

use MyAAC\Cache\Cache;

class Config
{
	public static function get()
	{
		return Cache::remember('config_server', 10 * 60, function () {
			if (file_exists(config('server_path') . Lua\Config::FILE)) {
				$config = new Lua\Config();
			}
			else {
				$config = new TOML\Config();
			}

			$config->load();

			return $config->get();
		});
	}

	public static function exists(): bool {
		return file_exists(config('server_path') . Lua\Config::FILE) || file_exists(config('server_path') . 'config/server.toml');
	}
}
