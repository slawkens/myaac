<?php

namespace MyAAC\Server\TOML;

use Devium\Toml\Toml;

class ExpStages
{
	private array $stages = [];

	const FILE = 'config/stages.toml';

	public function load(): void
	{
		$file = config('server_path') . self::FILE;

		if(!@file_exists($file)) {
			return;
		}

		$toml = file_get_contents($file);

		try {
			$stages = Toml::decode($toml, asArray: true);
		}
		catch (\Exception $e) {
			error('Error: Cannot load stages.toml. More info in system/logs/error.log file.');
			log_append('error.log', "[" . __CLASS__ . "] Fatal error: Cannot load stages.toml - $file. Error: " . $e->getMessage());
			return;
		}

		foreach ($stages['stage'] as $stage) {
			$this->stages[] = [
				'levels' => $stage['minlevel'] . (isset($stage['maxlevel']) ? '-' . $stage['maxlevel'] : '+'),
				'multiplier' => $stage['multiplier']
			];
		}

	}

	public function get(): array {
		return $this->stages;
	}
}
