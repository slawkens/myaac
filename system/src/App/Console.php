<?php

namespace MyAAC\App;

use MyAAC\Plugins;
use Symfony\Component\Console\Application;

class Console
{
	public function run(): void
	{
		if(!IS_CLI) {
			echo 'This script can be run only in command line mode.';
			exit(1);
		}

		require_once SYSTEM . 'functions.php';

		define('SELF_NAME', basename(__FILE__));

		$application = new Application('MyAAC', MYAAC_VERSION);

		$commandsGlob = glob(SYSTEM . 'src/Commands/*.php');
		foreach ($commandsGlob as $item) {
			$name = pathinfo($item, PATHINFO_FILENAME);
			if ($name == 'Command') { // ignore base Command class
				continue;
			}

			$commandPre = '\\MyAAC\Commands\\';
			$application->add(new ($commandPre . $name));
		}

		$pluginCommands = Plugins::getCommands();
		foreach ($pluginCommands as $item) {
			$application->add(require $item);
		}

		$application->run();
	}
}
