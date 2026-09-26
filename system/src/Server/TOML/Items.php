<?php

namespace MyAAC\Server\TOML;

use MyAAC\Cache\PHP as CachePHP;

class Items
{
	private string $error = '';
	private array $items = [];

	const FILE = 'items/items.toml';

	public function getError(): string {
		return $this->error;
	}

	public function load(): bool
	{
		$file = config('data_path') . 'items/items.toml';
		if (!file_exists($file)) {
			$this->error = 'Cannot load file ' . $file;
			return false;
		}

		//$toml = file_get_contents($file);
		//$items = \Devium\Toml\Toml::decode($toml, asArray: false);

		$itemsParser = new ItemsParser();
		$itemsParsed = $itemsParser->parse($file);

		foreach ($itemsParsed as $item) {
			$attributes = array_filter($item, function ($key) {
				return !in_array($key, ['id', 'article', 'name', 'plural']);
			}, ARRAY_FILTER_USE_KEY);

			$id = $item['id'] ?? null;
			if ($id === null) {
				continue;
			}

			$this->items[$id] = [
				'article' => $item['article'] ?? '',
				'name' => $item['name'] ?? '',
				'plural' => $item['plural'] ?? '',
				'attributes' => $attributes,
			];
		}

		$cache_php = new CachePHP(config('cache_prefix'), CACHE . 'persistent/');
		$cache_php->set('items', $this->items, 5 * 365 * 24 * 60 * 60);
		return true;
	}

	public function getItems(): array {
		return $this->items;
	}
}
