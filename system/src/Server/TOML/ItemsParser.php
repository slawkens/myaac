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

				$tmp = false;
				try {
					$tmp = parse_ini_string($parse);
				} catch (\Exception $e) {
					warning('Failed to parse items.toml line: ' . $i . ' with error: ' . $e->getMessage());
				}

				if ($tmp === false) {
					warning('Failed to parse items.toml line: ' . $i);
				}
				else {
					$ret[] = $tmp;
				}

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
			$tmp = parse_ini_string($parse);
			if ($tmp === false) {
				warning('Failed to parse items.toml line: ' . $i);
			} else {
				$ret[] = $tmp;
			}
		}

		fclose($handle);

		return $ret;
	}
}
