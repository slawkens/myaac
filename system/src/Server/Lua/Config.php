<?php

namespace MyAAC\Server\Lua;

class Config
{
	const FILE = 'config.lua';

	private array $config = [];

	public function load(): void
	{
		$file = config('server_path') . self::FILE;

		$this->config = Loader::load($file);
		if($this->config === false) {
			log_append('error.log', '[Config] Fatal error: Cannot load config.lua (' . $file . ').');
			throw new \RuntimeException('ERROR: Cannot find ' . $file . ' file.');
		}

		$this->init();
	}

	private function init(): void
	{
		if(isset($this->config['lua']['servername'])) {
			$this->config['lua']['serverName'] = $this->config['lua']['servername'];
		}

		if(isset($this->config['lua']['houserentperiod'])) {
			$this->config['lua']['houseRentPeriod'] = $this->config['lua']['houserentperiod'];
		}
	}

	public function get(): array {
		return $this->config;
	}
}
