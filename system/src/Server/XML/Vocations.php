<?php

namespace MyAAC\Server\XML;

class Vocations
{
	private array $vocations = [];
	private array $vocationsFrom = [];

	const FILE = 'vocations.xml';

	public function load(): void
	{
		$file = config('data_path') . 'XML/' . self::FILE;
		if(!@file_exists($file)) {
			$file = config('data_path') . self::FILE;
		}

		$xml = new \DOMDocument();
		if(!$xml->load($file)) {
			error('Error: Cannot load vocations.xml. More info in system/logs/error.log file.');
			log_append('error.log', "[" . __CLASS__ . "] Fatal error: Cannot load vocations.xml - $file. Error: " . print_r(error_get_last(), true));
			return;
		}

		foreach($xml->getElementsByTagName('vocation') as $vocation) {
			$id = $vocation->getAttribute('id');

			$this->vocations[$id] = $vocation->getAttribute('name');

			$fromVocation = (int) $vocation->getAttribute('fromvoc');
			$this->vocationsFrom[$id] = $fromVocation;
		}
	}

	public function get(): array {
		return $this->vocations;
	}

	public function getFrom(): array {
		return $this->vocationsFrom;
	}
}
