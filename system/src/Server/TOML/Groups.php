<?php

namespace MyAAC\Server\TOML;

use Devium\Toml\Toml;

class Groups
{
	private array $groups;

	public function load(): void
	{
		$file = config('server_path') . 'config/groups.toml';

		if(!@file_exists($file)) {
			error('Error: Cannot load groups.toml. More info in system/logs/error.log file.');
			log_append('error.log', '[OTS_Groups_List.php] Fatal error: Cannot load groups.toml (' . $file . '). It doesnt exist.');
			return;
		}

		$toml = file_get_contents($file);
		$groups = Toml::decode($toml, asArray: true);

		foreach ($groups as $group)
		{
			$this->groups[$group['id']] = [
				'id' => $group['id'],
				'name' => $group['name'],
				'access' => $group['access'],
			];
		}
	}

	public function getGroups(): array {
		return $this->groups;
	}
}
