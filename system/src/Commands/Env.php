<?php

namespace MyAAC\Commands;

use POT;

trait Env
{
	protected function init(): void
	{
		global $config;
		if (!isset($config['installed']) || !$config['installed']) {
			throw new \RuntimeException('MyAAC has not been installed yet or there was error during installation. Please install again.');
		}

		if(empty($config['server_path'])) {
			throw new \RuntimeException('Server Path has been not set. Go to config.php and set it.');
		}

		// take care of trailing slash at the end
		if($config['server_path'][strlen($config['server_path']) - 1] !== '/')
			$config['server_path'] .= '/';

		$config['lua'] = load_config_lua($config['server_path'] . 'config.lua');

		// POT
		require_once SYSTEM . 'libs/pot/OTS.php';
		$ots = POT::getInstance();
		$eloquentConnection = null;

		require_once SYSTEM . 'database.php';
	}
}
