<?php

namespace MyAAC\Server\XML;

use MyAAC\Cache\PHP as CachePHP;

class Items
{
	private string $error;

	public function getError(): string {
		return $this->error;
	}

	public function load(): bool
	{
		$file_path = config('data_path') . 'items/items.xml';
		if (!file_exists($file_path)) {
			$this->error = 'Cannot load file ' . $file_path;
			return false;
		}

		$xml = new \DOMDocument;
		$xml->load($file_path);

		$items = [];
		foreach ($xml->getElementsByTagName('item') as $item) {
			if ($item->getAttribute('fromid')) {
				for ($id = $item->getAttribute('fromid'); $id <= $item->getAttribute('toid'); $id++) {
					$tmp = $this->parseNode($id, $item);
					$items[$tmp['id']] = $tmp['content'];
				}
			} else {
				$tmp = $this->parseNode($item->getAttribute('id'), $item);
				$items[$tmp['id']] = $tmp['content'];
			}
		}

		$cache_php = new CachePHP(config('cache_prefix'), CACHE . 'persistent/');
		$cache_php->set('items', $items, 5 * 365 * 24 * 60 * 60);
		return true;
	}

	public function parseNode($id, $node): array
	{
		$name = $node->getAttribute('name');
		$article = $node->getAttribute('article');
		$plural = $node->getAttribute('plural');

		$attributes = array();
		foreach($node->getElementsByTagName('attribute') as $attr) {
			$attributes[strtolower($attr->getAttribute('key'))] = $attr->getAttribute('value');
		}

		return ['id' => $id, 'content' => ['article' => $article, 'name' => $name, 'plural' => $plural, 'attributes' => $attributes]];
	}
}
