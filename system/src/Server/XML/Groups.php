<?php

namespace MyAAC\Server\XML;

class Groups
{
	private array $groups = [];

	public function load(): void
	{
		$file = config('data_path') . 'XML/groups.xml';

		if(!@file_exists($file)) {
			error('Error: Cannot load groups.xml. More info in system/logs/error.log file.');
			log_append('error.log', "[OTS_Groups_List.php] Fatal error: Cannot load groups.xml ($file). It doesn't exist.");
			return;
		}

		$groups = new \DOMDocument();
		if(!@$groups->load($file)) {
			error('Error: Cannot load groups.xml. More info in system/logs/error.log file.');
			log_append('error.log', '[OTS_Groups_List.php] Fatal error: Cannot load groups.xml (' . $file . '). Error: ' . print_r(error_get_last(), true));
			return;
		}

		// loads groups
		foreach( $groups->getElementsByTagName('group') as $group)
		{
			$this->groups[$group->getAttribute('id')] = [
				'id' => $group->getAttribute('id'),
				'name' => $group->getAttribute('name'),
				'access' => $group->getAttribute('access')
			];
		}
	}

	public function getGroups(): array {
		return $this->groups;
	}
}
