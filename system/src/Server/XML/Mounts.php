<?php

namespace MyAAC\Server\XML;

class Mounts
{
	private array $mounts = [];

	const FILE = 'XML/mounts.xml';

	public function load(): void
	{
		$file = config('data_path') . self::FILE;

		if(!@file_exists($file)) {
			return;
		}

		$xml = new \DOMDocument();
		if(!$xml->load($file)) {
			error('Error: Cannot load mounts.xml. More info in system/logs/error.log file.');
			log_append('error.log', "[" . __CLASS__ . "] Fatal error: Cannot load mounts.xml - $file. Error: " . print_r(error_get_last(), true));
			return;
		}

		foreach ($xml->getElementsByTagName('mount') as $mount) {
			$this->mounts[] = $this->parseMountNode($mount);
		}
	}

	private function parseMountNode($node): array
	{
		$id = (int)$node->getAttribute('id');
		$client_id = (int)$node->getAttribute('clientid');
		$name = $node->getAttribute('name');
		$speed = (int)$node->getAttribute('speed');
		$premium = getBoolean($node->getAttribute('premium'));
		$type = $node->getAttribute('type');

		return [
			'id' => $id,
			'client_id' => $client_id,
			'name' => $name,
			'speed' => $speed,
			'premium' => $premium,
			'type' => $type
		];
	}

	public function get(): array {
		return $this->mounts;
	}
}
