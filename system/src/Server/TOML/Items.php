<?php

namespace MyAAC\Server\TOML;

use MyAAC\Cache\PHP as CachePHP;

class Items
{
	private string $error;

	public function getError(): string
	{
		return $this->error;
	}

	public function load(): bool
	{
		$file_path = config('data_path') . 'items/items.toml';
		if (!file_exists($file_path)) {
			$this->error = 'Cannot load file ' . $file_path;
			return false;
		}

		//$toml = file_get_contents($file_path);
		//$items = \Devium\Toml\Toml::decode($toml, asArray: false);

		$itemsParser = new ItemsParser();
		$itemsParsed = $itemsParser->parse($file_path);

		$items = [];
		foreach ($itemsParsed as $item) {
			$attributes = array_filter($item, function ($key) {
				return !in_array($key, ['id', 'article', 'name', 'plural']);
			}, ARRAY_FILTER_USE_KEY);

			$id = $item['id'] ?? null;
			if ($id === null) {
				continue;
			}

			$article = $item['article'] ?? '';
			$name = $item['name'] ?? '';
			$plural = $item['plural'] ?? '';
			$items[$id] = [
				'article' => $article,
				'name' => $name,
				'plural' => $plural,
				'attributes' => $attributes,
			];
		}

		$cache_php = new CachePHP(config('cache_prefix'), CACHE . 'persistent/');
		$cache_php->set('items', $items, 5 * 365 * 24 * 60 * 60);
		return true;
	}
}
