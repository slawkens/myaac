<?php

namespace MyAAC\Server\TOML;

use Devium\Toml\Toml;

class Outfits
{
	private array $outfits = [];

	const FILE = 'config/outfits.toml';

	public function load(): void
	{
		$file = config('server_path') . self::FILE;

		if(!@file_exists($file)) {
			return;
		}

		$toml = file_get_contents($file);

		try {
			$outfits = Toml::decode($toml, asArray: true);
		}
		catch (\Exception $e) {
			error('Error: Cannot load outfits.toml. More info in system/logs/error.log file.');
			log_append('error.log', "[" . __CLASS__ . "] Fatal error: Cannot load outfits.toml - $file. Error: " . $e->getMessage());
			return;
		}

		foreach ($outfits as $outfit)
		{
			$this->outfits[] = [
				'id' => $outfit['id'],
				'sex' => ($outfit['sex'] == 'male' ? SEX_MALE : SEX_FEMALE),
				'name' => $outfit['name'],
				'premium' => $outfit['premium'] ?? false,
				'locked' => $outfit['locked'] ?? false,
				'enabled' => $outfit['enabled'] ?? true,
			];
		}
	}

	public function get(): array {
		return $this->outfits;
	}
}
