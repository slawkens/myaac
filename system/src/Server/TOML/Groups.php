<?php

namespace MyAAC\Server\TOML;

use Devium\Toml\Toml;

class Groups
{
	private array $groups = [];

	const FILE = 'config/groups.toml';

	public function load(): void
	{
		$file = config('server_path') . self::FILE;

		if(!@file_exists($file)) {
			error('Error: Cannot load groups.toml. More info in system/logs/error.log file.');
			log_append('error.log', "[" . __CLASS__ . "] Fatal error: Cannot load groups.toml - $file. It doesn't exist.");
			return;
		}

		$toml = file_get_contents($file);

		try {
			$groups = Toml::decode($toml, asArray: true);
		}
		catch (\Exception $e) {
			error('Error: Cannot load groups.toml. More info in system/logs/error.log file.');
			log_append('error.log', "[" . __CLASS__ . "] Fatal error: Cannot load groups.toml - $file. Error: " . $e->getMessage());
			return;
		}

		foreach ($groups as $group)
		{
			$this->groups[$group['id']] = [
				'id' => $group['id'],
				'name' => $group['name'],
				'access' => $group['access'],
			];
		}
	}

	public function get(): array {
		return $this->groups;
	}
}
