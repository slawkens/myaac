<?php

namespace MyAAC\Server\Lua;

class ExpStages
{
	private array $stages = [];

	const FILE = 'stages.lua';

	public function load(): void
	{
		$file = config('data_path') . self::FILE;

		if(!@file_exists($file)) {
			return;
		}

		if (!extension_loaded('lua')) {
			return;
		}

		$lua = new \Lua();
		try {
			$stagesContent = file_get_contents($file);
			$stagesContent .= 'return experienceStages';
			$stages = $lua->eval($stagesContent);
		}
		catch (\Exception $e) {
			error('Error: Cannot load stages.lua. More info in system/logs/error.log file.');
			log_append('error.log', "[" . __CLASS__ . "] Fatal error: Cannot load stages.lua - $file. Error: " . $e->getMessage());
			return;
		}

		foreach ($stages as $stage) {
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
