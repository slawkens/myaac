<?php

namespace MyAAC\Server\XML;

class ExpStages
{
	private array $stages = [];

	const FILE = 'XML/stages.xml';

	public function load(): void
	{
		$file = config('data_path') . self::FILE;

		if(!@file_exists($file)) {
			return;
		}

		$xml = new \DOMDocument();
		if(!$xml->load($file)) {
			error('Error: Cannot load stages.xml. More info in system/logs/error.log file.');
			log_append('error.log', "[" . __CLASS__ . "] Fatal error: Cannot load stages.xml - $file. Error: " . print_r(error_get_last(), true));
			return;
		}

		foreach($xml->getElementsByTagName('stage') as $stage)
		{
			/** @var \DOMElement $stage */
			$maxLevel = $stage->getAttribute('maxlevel');
			$this->stages[] = [
				'levels' => $stage->getAttribute('minlevel') . (isset($maxLevel[0]) ? '-' . $maxLevel : '+'),
				'multiplier' => $stage->getAttribute('multiplier')
			];
		}
	}

	public function get(): array {
		return $this->stages;
	}
}
