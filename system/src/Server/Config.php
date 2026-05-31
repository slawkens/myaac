<?php

namespace MyAAC\Server;

use MyAAC\Cache\Cache;

class Config
{
	public static function get()
	{
		$cache = Cache::getInstance();

		// load otserv config file
		if($cache->enabled()) {
			$tmp = null;
			if(!$cache->fetch('server_path', $tmp) || $tmp != config('server_path')) {
				$cache->delete('config_server');

				clearCache();
			}
		}

		return Cache::remember('config_server', 10 * 60, function () use ($cache) {
			if (file_exists(config('server_path') . Lua\Config::FILE)) {
				$config = new Lua\Config();
			}
			else {
				$config = new TOML\Config();
			}

			$config->load();

			if($cache->enabled()) {
				$cache->set('server_path', config('server_path'), 10 * 60);
			}

			return $config->get();
		});
	}

	public static function exists(): bool {
		return file_exists(config('server_path') . Lua\Config::FILE) || file_exists(config('server_path') . 'config/server.toml');
	}
}
