<?php

namespace MyAAC\Server\XML;

use MyAAC\Cache\Cache;

class Vocations
{
	private static array $vocations;
	private static array $vocationsFrom;

	public function __construct()
	{
		$cached = Cache::remember('vocations', 10 * 60, function () {
			$this->load();
			$from = $this->getFrom();

			$amount = 0;
			foreach ($from as $vocId => $fromVocation) {
				if ($vocId != 0 && $vocId == $fromVocation) {
					$amount++;
				}
			}

			return ['vocations' => $this->get(), 'vocationsFrom' => $from, 'amount' => $amount];
		});

		self::$vocations = $cached['vocations'];
		self::$vocationsFrom = $cached['vocationsFrom'];

		config(['vocations', self::$vocations]);
		config(['vocations_amount', $cached['amount']]);
	}

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

			self::$vocations[$id] = $vocation->getAttribute('name');

			$fromVocation = (int) $vocation->getAttribute('fromvoc');
			self::$vocationsFrom[$id] = $fromVocation;
		}
	}

	public static function get(): array {
		return self::$vocations;
	}

	public static function getFrom(): array {
		return self::$vocationsFrom;
	}

	public static function getPromoted(int $id): ?int {
		foreach (self::$vocationsFrom as $vocId => $fromVocation) {
			if ($id == $fromVocation && $vocId != $id) {
				return $vocId;
			}
		}

		return null;
	}

	public static function getOriginal(int $id): ?int {
		return self::$vocationsFrom[$id] ?? null;
	}

	public static function getBase($includingRook = true): array {
		$vocations = [];
		foreach (self::$vocationsFrom as $vocId => $fromVoc) {
			if ($vocId == $fromVoc && ($vocId != 0 || $includingRook)) {
				$vocations[] = $vocId;
			}
		}

		return $vocations;
	}
}
