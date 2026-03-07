<?php

namespace MyAAC\Server\XML;

use MyAAC\Cache\PHP as CachePHP;

class Items
{
	private string $error = '';

	const FILE = 'items/items.xml';

	public function getError(): string {
		return $this->error;
	}

	public function load(): bool
	{
		$file = config('data_path') . self::FILE;
		if (!file_exists($file)) {
			$this->error = 'Cannot load file ' . $file;
			return false;
		}

		$items = [];

		try {
			$xml = new \SimpleXMLElement(file_get_contents($file));
		} catch (\Exception $e) {
			$this->error = 'Error: Cannot load items.xml. More info in system/logs/error.log file.';
			log_append('error.log', "[" . __CLASS__ . "] Fatal error: Cannot load items.xml - $file. Error: " . $e->getMessage());
			return false;
		}

		foreach($xml->xpath('item') as $item) {
			if ($item->attributes()->fromid) {
				for ($id = (int)$item->attributes()->fromid; $id <= (int)$item->attributes()->toid; $id++) {
					$tmp = $this->parseNode($id, $item);
					$items[$tmp['id']] = $tmp['content'];
				}
			} else {
				$tmp = $this->parseNode($item->attributes()->id, $item);
				$items[$tmp['id']] = $tmp['content'];
			}
		}

		$cache_php = new CachePHP(config('cache_prefix'), CACHE . 'persistent/');
		$cache_php->set('items', $items, 5 * 365 * 24 * 60 * 60);
		return true;
	}

	public function parseNode($id, $node): array
	{
		$name = $node->attributes()->name;
		$article = $node->attributes()->article;
		$plural = $node->attributes()->plural;

		$attributes = [];
		foreach($node->xpath('attribute') as $attr) {
			$attributes[strtolower($attr->attributes()->key)] = (string)$attr->attributes()->value;
		}

		return [
			'id' => (int)$id,
			'content' => [
				'article' => (string)$article,
				'name' => (string)$name,
				'plural' => (string)$plural,
				'attributes' => $attributes
			],
		];
	}
}
