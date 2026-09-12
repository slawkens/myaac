<?php

namespace MyAAC\Server;

use MyAAC\Cache\Cache;

class Vocations
{
	private static array $vocations = [];
	private static array $vocationsFrom = [];

	public function __construct() {
		$cached = Cache::remember('vocations', 10 * 60, function () {
			$tomlVocations = glob(config('data_path') . 'vocations/*.toml');
			if (count($tomlVocations) > 0) {
				$vocations = new TOML\Vocations();
			}
			else {
				$vocations = new XML\Vocations();
			}

			$vocations->load();
			$from = $vocations->getFrom();

			$amount = 0;
			foreach ($from as $vocId => $fromVocation) {
				if ($vocId != 0 && $vocId == $fromVocation) {
					$amount++;
				}
			}

			return ['vocations' => $vocations->get(), 'vocationsFrom' => $from, 'amount' => $amount];
		});

		self::$vocations = $cached['vocations'];
		self::$vocationsFrom = $cached['vocationsFrom'];

		config(['vocations', self::$vocations]);
		config(['vocations_amount', $cached['amount']]);
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
		if (!isset(self::$vocationsFrom[$id])) {
			return null;
		}

		while ($tmpId = self::$vocationsFrom[$id]) {
			if ($tmpId == $id) {
				break;
			}

			$id = $tmpId;
		}

		return $id;
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
