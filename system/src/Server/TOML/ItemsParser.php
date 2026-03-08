<?php

namespace MyAAC\Server\TOML;

class ItemsParser
{
	public function parse(string $path): array
	{
		$ret = [];

		$i = 0;
		$handle = fopen($path, 'r');

		if ($handle === false) {
			throw new \RuntimeException('Failed to open items file: ' . $path);
		}

		$parse = '';

		while (($line = fgets($handle)) !== false) {
			if (str_contains($line, '[[items]]') && $i++ != 0) {
				//global $whoopsHandler;
				//$whoopsHandler->addDataTable('ini', [$parse]);
				$ret[] = parse_ini_string($parse);
				$parse = '';
				continue;
			}

			// skip lines like this
			// field = {type = "fire", initdamage = 20, ticks = 10000, count = 7, damage = 10}
			// as it cannot be parsed by parse_ini_string
			if (str_starts_with(ltrim($line), 'field =')) {
				continue;
			}

			$parse .= $line;
		}

		if ($parse !== '') {
			$ret[] = parse_ini_string($parse);
		}

		fclose($handle);

		return $ret;
	}
}
