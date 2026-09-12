<?php

namespace MyAAC\Server\TOML;

use Devium\Toml\Toml;

class Mounts
{
	private array $mounts = [];

	const FILE = 'config/mounts.toml';

	public function load(): void
	{
		$file = config('server_path') . self::FILE;

		if(!@file_exists($file)) {
			return;
		}

		$toml = file_get_contents($file);

		try {
			$mounts = Toml::decode($toml, asArray: true);
		}
		catch (\Exception $e) {
			error('Error: Cannot load mounts.toml. More info in system/logs/error.log file.');
			log_append('error.log', "[" . __CLASS__ . "] Fatal error: Cannot load mounts.toml - $file. Error: " . $e->getMessage());
			return;
		}

		foreach ($mounts as $name => $mount)
		{
			$this->mounts[] = [
				'id' => $mount['id'],
				'client_id' => $mount['clientid'] ?? false,
				'name' => $name,
				'speed' => $mount['speed'] ?? 0,
				'premium' => $mount['premium'] ?? false,
			];
		}
	}

	public function get(): array {
		return $this->mounts;
	}
}
