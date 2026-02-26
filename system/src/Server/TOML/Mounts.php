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
		$mounts = Toml::decode($toml, asArray: true);

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
