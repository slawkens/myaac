<?php

namespace MyAAC\Server\TOML;

use Devium\Toml\Toml;
use RuntimeException;

class Config
{
	private array $config = [];

	public function load(): void
	{
		$path = config('server_path') . 'config/';
		$files = glob($path . '*.toml');

		// filter files we don't need
		$ignore = ['account_manager', 'gameplay', 'groups', 'mounts', 'object_pools', 'outfits', 'scripts'];
		$files = array_filter($files, function ($file) use ($ignore) {
			foreach ($ignore as $item) {
				if (str_contains($file, $item)) {
					return false;
				}
			}

			return true;
		});

		foreach ($files as $file) {
			$key = basename($file, '.toml');

			$toml = file_get_contents($file);

			try {
				$this->config[$key] = Toml::decode($toml, asArray: true);
			}
			catch (\Exception $e) {
				throw new RuntimeException("Error: Cannot load config/$key.toml. More info in system/logs/error.log file.");
				log_append('error.log', "[" . __CLASS__ . "] Fatal error: Cannot load config/$key.toml - $file. Error: " . $e->getMessage());
				return;
			}
		}

		$this->config['serverName'] = $this->config['server']['identity']['name'] ?? 'Unknown';
		$this->config['freePremium'] = $this->config['server']['accounts']['free_premium'] ?? false;
		$this->config['ip'] = $this->config['server']['network']['ip'] ?? '127.0.0.1';
		$this->config['worldType'] = $this->config['server']['world']['type'] ?? 'unknown';
	}

	public function get(): array {
		return $this->config;
	}
}

