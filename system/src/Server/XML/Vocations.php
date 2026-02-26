<?php

namespace MyAAC\Server\XML;

class Vocations
{
	private array $vocations = [];
	private array $vocationsFrom = [];

	public function load(): void
	{
		if(!class_exists('DOMDocument')) {
			throw new \RuntimeException('Please install PHP xml extension. MyAAC will not work without it.');
		}

		$vocationsXML = new \DOMDocument();
		$file = config('data_path') . 'XML/vocations.xml';
		if(!@file_exists($file)) {
			$file = config('data_path') . 'vocations.xml';
		}

		if(!$vocationsXML->load($file)) {
			throw new \RuntimeException('ERROR: Cannot load <i>vocations.xml</i> - the file is malformed. Check the file with xml syntax validator.');
		}

		foreach($vocationsXML->getElementsByTagName('vocation') as $vocation) {
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
