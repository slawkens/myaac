<?php

namespace MyAAC\Server\TOML;

use Devium\Toml\Toml;

class Vocations
{
	private array $vocations = [];
	private array $vocationsFrom = [];

	public function load(): void
	{
		$tomlVocations = glob(config('data_path') . 'vocations/*.toml');
		if (count($tomlVocations) <= 0) {
			throw new \RuntimeException('ERROR: Cannot load any .toml vocation from the data/vocations folder.');
		}

		foreach ($tomlVocations as $file) {
			$toml = file_get_contents($file);

			try {
				$vocations = Toml::decode($toml, asArray: true);
			}
			catch (\Exception $e) {
				$basename = basename($file);
				error("Error: Cannot load vocations/$basename. More info in system/logs/error.log file.");
				log_append('error.log', "[" . __CLASS__ . "] Fatal error: Cannot load mounts.toml - $file. Error: " . $e->getMessage());
				break;
			}

			foreach ($vocations as $vocationArray) {
				$id = $vocationArray['id'];

				$this->vocations[$id] = $vocationArray['name'];
				$this->vocationsFrom[$id] = $vocationArray['promotedfrom'];
			}
		}

		ksort($this->vocations, SORT_NUMERIC);
		ksort($this->vocationsFrom, SORT_NUMERIC);
	}

	public function get(): array {
		return $this->vocations;
	}

	public function getFrom(): array {
		return $this->vocationsFrom;
	}
}
