<?php

namespace MyAAC\Server\XML;

class Outfits
{
	private array $outfits = [];

	const FILE = 'XML/outfits.xml';

	public function load(): void
	{
		$file = config('data_path') . self::FILE;

		if(!@file_exists($file)) {
			return;
		}

		$xml = new \DOMDocument;
		$xml->load($file);

		foreach ($xml->getElementsByTagName('outfit') as $outfit) {
			$this->outfits[] = $this->parseOutfitNode($outfit);
		}
	}

	private function parseOutfitNode($node): array
	{
		$looktype = (int)$node->getAttribute('looktype');
		$type = (int)$node->getAttribute('type');
		$name = $node->getAttribute('name');
		$premium = getBoolean($node->getAttribute('premium'));
		$locked = !getBoolean($node->getAttribute('unlocked'));
		$enabled = getBoolean($node->getAttribute('enabled'));

		return [
			'id' => $looktype,
			'sex' => ($type === 1 ? SEX_MALE : SEX_FEMALE),
			'name' => $name,
			'premium' => $premium,
			'locked' => $locked,
			'enabled' => $enabled,
		];
	}

	public function getOutfits(): array {
		return $this->outfits;
	}
}
